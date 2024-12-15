using System.IdentityModel.Tokens.Jwt;
using System.Security.Claims;
using System.Text;
using Ktu.T120B178.Api.Configurations;
using Microsoft.IdentityModel.Tokens;

namespace Ktu.T120B178.Api.Utils;

/// <summary>
/// <para>Helpers related to working with JTWs.</para>
/// <para>Static members are thread safe, instance members are not.</para>
/// </summary>
public static class JwtUtil 
{
	/// <summary>
	/// Build a JWT token.
	/// </summary>
	/// <param name="claims">Claims. Fields to store in the token.</param>
	/// <param name="secret">Secret for hashing. At least 16 symbols long.</param>
	/// <param name="duration">How long the token should be considered valid.</param>
	/// <returns>JWT token built.</returns>
	public static JwtSecurityToken CreateToken(List<Claim> claims, string secret, TimeSpan duration)
	{
		//validate inputs
		if( claims == null )
			throw new ArgumentException("Argument 'claims' is null.");

		if( secret == null )
			throw new ArgumentException("Argument 'secret' is null.");

		if( secret.Length < 16 )
			throw new ArgumentException("Argument 'secret' must contain a string at least 16 symbols long.");

		//derive signing credentials from the secret
		var signingKey = new SymmetricSecurityKey(Encoding.UTF8.GetBytes(Config.JwtSecret));
		var signingCredentials = new SigningCredentials(signingKey, SecurityAlgorithms.HmacSha256);

		//creat the token
		var token = 
			new JwtSecurityToken(
				issuer: "",
				audience: "",
				expires: DateTime.Now + duration,
				claims: claims,
				signingCredentials: signingCredentials
			);

		//
		return token;
	}

	/// <summary>
	/// Serialize given token to string.
	/// </summary>
	/// <param name="token">Token to serialize.</param>
	/// <returns>Serialized token.</returns>
	public static string SerializeToken(JwtSecurityToken token)
	{
		//validate inputs
		if( token ==  null )
			throw new ArgumentException("Argument 'token' is null.");

		//serialize
		var res = new JwtSecurityTokenHandler().WriteToken(token);

		//
		return res;
	}
}