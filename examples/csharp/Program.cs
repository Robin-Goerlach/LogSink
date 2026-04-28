using System.Net;
using System.Net.Http.Headers;
using System.Text;
using System.Text.Json;

namespace LogSink.CSharpExamples;

/// <summary>
/// Einfache C#-Beispiele für den aktuellen LogSink-V0/V1-Service.
/// </summary>
internal static class Program
{
    private const string DefaultLogSinkUrl = "http://api.sasd.de/logsink/index.php";

    public static async Task<int> Main(string[] args)
    {
        string command = args.Length > 0 ? args[0].Trim().ToLowerInvariant() : "help";
        string baseUrl = Environment.GetEnvironmentVariable("LOGSINK_URL") ?? DefaultLogSinkUrl;

        using LogSinkHttpClient client = new(baseUrl);

        try
        {
            return command switch
            {
                "send-json" => await SendJsonAsync(client),
                "send-text" => await SendTextAsync(client),
                "send-error" => await SendErrorAsync(client),
                "read" => await ReadAsync(client, args),
                "roundtrip" => await RoundtripAsync(client),
                "help" or "--help" or "-h" => ShowHelp(),
                _ => ShowUnknownCommand(command)
            };
        }
        catch (Exception exception)
        {
            Console.Error.WriteLine("ERROR: " + exception.Message);
            return 1;
        }
    }

    private static async Task<int> SendJsonAsync(LogSinkHttpClient client)
    {
        string payload = JsonSerializer.Serialize(
            new
            {
                timestamp = DateTimeOffset.Now,
                level = "INFO",
                service = "csharp-json-sender",
                message = "Hello from C# JSON sender",
                context = new
                {
                    example = "send-json",
                    project = "LogSink",
                    senderType = "csharp"
                }
            },
            JsonOptions.Pretty
        );

        Console.WriteLine("POST " + client.BaseUrl);
        Console.WriteLine(payload);
        Console.WriteLine();

        HttpResult result = await client.PostAsync(
            payload,
            "application/json; charset=utf-8",
            "SASD-csharp-json-sender/0.1"
        );

        result.Print();
        return result.IsSuccessful ? 0 : 1;
    }

    private static async Task<int> SendTextAsync(LogSinkHttpClient client)
    {
        string payload = $"{DateTimeOffset.Now:O} INFO csharp-text-sender Hello from plain text C# sender";

        Console.WriteLine("POST " + client.BaseUrl);
        Console.WriteLine(payload);
        Console.WriteLine();

        HttpResult result = await client.PostAsync(
            payload,
            "text/plain; charset=utf-8",
            "SASD-csharp-text-sender/0.1"
        );

        result.Print();
        return result.IsSuccessful ? 0 : 1;
    }

    private static async Task<int> SendErrorAsync(LogSinkHttpClient client)
    {
        string payload = JsonSerializer.Serialize(
            new
            {
                timestamp = DateTimeOffset.Now,
                level = "ERROR",
                service = "csharp-error-sender",
                message = "Simulated error from C# sender",
                context = new
                {
                    example = "send-error",
                    exception = "DemoException",
                    hint = "This is a demo error, not a real application failure."
                }
            },
            JsonOptions.Pretty
        );

        Console.WriteLine("POST " + client.BaseUrl);
        Console.WriteLine(payload);
        Console.WriteLine();

        HttpResult result = await client.PostAsync(
            payload,
            "application/json; charset=utf-8",
            "SASD-csharp-error-sender/0.1"
        );

        result.Print();
        return result.IsSuccessful ? 0 : 1;
    }

    private static async Task<int> ReadAsync(LogSinkHttpClient client, string[] args)
    {
        int limit = 5;

        if (args.Length > 1 && int.TryParse(args[1], out int parsedLimit))
        {
            limit = parsedLimit;
        }

        limit = Math.Clamp(limit, 1, 1000);

        Console.WriteLine($"GET {client.BaseUrl}?limit={limit}");
        Console.WriteLine();

        HttpResult result = await client.GetLatestAsync(limit);
        result.Print();

        return result.IsSuccessful ? 0 : 1;
    }

    private static async Task<int> RoundtripAsync(LogSinkHttpClient client)
    {
        string runId = $"logsink-csharp-smoke-{DateTimeOffset.Now:yyyyMMddTHHmmss}-{Random.Shared.Next(1000, 10000)}";

        string payload = JsonSerializer.Serialize(
            new
            {
                timestamp = DateTimeOffset.Now,
                level = "INFO",
                service = "csharp-roundtrip-smoke-test",
                message = "Roundtrip smoke test from C# sender",
                context = new
                {
                    runId,
                    expectedFlow = "csharp sender -> service -> database -> reader"
                }
            },
            JsonOptions.Pretty
        );

        Console.WriteLine("POST " + client.BaseUrl);
        Console.WriteLine("RUN_ID=" + runId);
        Console.WriteLine();

        HttpResult postResult = await client.PostAsync(
            payload,
            "application/json; charset=utf-8",
            "SASD-csharp-roundtrip-smoke-test/0.1"
        );

        postResult.Print();

        if (!postResult.IsSuccessful)
        {
            Console.Error.WriteLine("ERROR: POST failed.");
            return 1;
        }

        Console.WriteLine();
        Console.WriteLine($"GET {client.BaseUrl}?limit=10");
        Console.WriteLine();

        HttpResult getResult = await client.GetLatestAsync(10);
        getResult.Print();

        if (!getResult.IsSuccessful)
        {
            Console.Error.WriteLine("ERROR: GET failed.");
            return 1;
        }

        if (getResult.Body.Contains(runId, StringComparison.Ordinal))
        {
            Console.WriteLine();
            Console.WriteLine("OK: Roundtrip message was found.");
            return 0;
        }

        Console.Error.WriteLine();
        Console.Error.WriteLine("ERROR: Roundtrip message was not found in latest logs.");
        return 1;
    }

    private static int ShowHelp()
    {
        Console.WriteLine("""
            LogSink C# examples

            Usage:
              dotnet run --project examples/csharp -- send-json
              dotnet run --project examples/csharp -- send-text
              dotnet run --project examples/csharp -- send-error
              dotnet run --project examples/csharp -- read 10
              dotnet run --project examples/csharp -- roundtrip

            Configuration:
              LOGSINK_URL=http://api.sasd.de/logsink/index.php
            """);

        return 0;
    }

    private static int ShowUnknownCommand(string command)
    {
        Console.Error.WriteLine("Unknown command: " + command);
        Console.Error.WriteLine();
        ShowHelp();
        return 1;
    }
}

/// <summary>
/// Kleine HTTP-Client-Kapsel für die LogSink-Beispiele.
/// </summary>
internal sealed class LogSinkHttpClient : IDisposable
{
    private readonly HttpClient _httpClient;

    public LogSinkHttpClient(string baseUrl)
    {
        BaseUrl = string.IsNullOrWhiteSpace(baseUrl)
            ? "http://api.sasd.de/logsink/index.php"
            : baseUrl.Trim();

        _httpClient = new HttpClient
        {
            Timeout = TimeSpan.FromSeconds(15)
        };
    }

    public string BaseUrl { get; }

    public async Task<HttpResult> PostAsync(string payload, string contentType, string userAgent)
    {
        using StringContent content = new(payload, Encoding.UTF8);
        content.Headers.ContentType = MediaTypeHeaderValue.Parse(contentType);

        using HttpRequestMessage request = new(HttpMethod.Post, BaseUrl)
        {
            Content = content
        };

        request.Headers.Accept.Add(new MediaTypeWithQualityHeaderValue("application/json"));
        request.Headers.UserAgent.ParseAdd(userAgent);

        using HttpResponseMessage response = await _httpClient.SendAsync(request);
        string body = await response.Content.ReadAsStringAsync();

        return new HttpResult(response.StatusCode, body);
    }

    public async Task<HttpResult> GetLatestAsync(int limit)
    {
        int safeLimit = Math.Clamp(limit, 1, 1000);
        string separator = BaseUrl.Contains('?') ? "&" : "?";
        string url = BaseUrl + separator + "limit=" + safeLimit;

        using HttpRequestMessage request = new(HttpMethod.Get, url);

        request.Headers.Accept.Add(new MediaTypeWithQualityHeaderValue("application/json"));
        request.Headers.UserAgent.ParseAdd("SASD-csharp-log-reader/0.1");

        using HttpResponseMessage response = await _httpClient.SendAsync(request);
        string body = await response.Content.ReadAsStringAsync();

        return new HttpResult(response.StatusCode, body);
    }

    public void Dispose()
    {
        _httpClient.Dispose();
    }
}

internal readonly record struct HttpResult(HttpStatusCode StatusCode, string Body)
{
    public bool IsSuccessful => (int)StatusCode >= 200 && (int)StatusCode < 300;

    public void Print()
    {
        Console.WriteLine($"HTTP {(int)StatusCode} {StatusCode}");
        Console.WriteLine();
        Console.WriteLine(Body);
    }
}

internal static class JsonOptions
{
    public static readonly JsonSerializerOptions Pretty = new()
    {
        WriteIndented = true
    };
}
