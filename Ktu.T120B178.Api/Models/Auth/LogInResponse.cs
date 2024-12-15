namespace Ktu.T120B178.Api.Models.Auth;

public class LogInResponse
{
    /// <summary>
    /// User ID.
    /// </summary>
    public int UserId { get; init; }

    /// <summary>
    /// User title.
    /// </summary>
    public string UserTitle { get; init; } = null!;

    /// <summary>
    /// JWT for subsequent authentication.
    /// </summary>
    public string Jwt { get; init; } = null!;
}