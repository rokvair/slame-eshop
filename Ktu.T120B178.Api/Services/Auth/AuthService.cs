using System.Security.Claims;
using Ktu.T120B178.Api.Configurations;
using Ktu.T120B178.Api.Models.Auth;
using Ktu.T120B178.Api.Utils;

namespace Ktu.T120B178.Api.Services.Auth;

public sealed class AuthService : IAuthService
{
	public Task<LogInResponse?> Login(LoginModel model)
	{
		//validate inputs
		if (model.UserName != "a" || model.Password != "b")
		{
			return Task.FromResult(null as LogInResponse);
		}

		var token = GenerateToken(new KeyValuePair<string, string>("userId", "1"));

		var result = new LogInResponse()
		{
			UserId = 1,
			UserTitle = model.UserName,
			Jwt = token
		};
            
		return Task.FromResult(result)!;
	}

	private string GenerateToken(params KeyValuePair<string, string>[] claims)
	{
		//create JWT token containing user permissions and other info
		var tokenClaims = new List<Claim>
		{
			new(ClaimTypes.Role, "user")
		};
		var additionalClaims = claims
			.Select(customClaim => new Claim(customClaim.Key, customClaim.Value));
		tokenClaims.AddRange(additionalClaims);

		var token = JwtUtil.CreateToken(tokenClaims, Config.JwtSecret, TimeSpan.FromHours(8));
		var tokenString = JwtUtil.SerializeToken(token);

		return tokenString;
	}

	public Task Logout() => Task.CompletedTask;
}