using Ktu.T120B178.Api.Common;
using Ktu.T120B178.Api.Models.Auth;
using Ktu.T120B178.Api.Services.Auth;

namespace Ktu.T120B178.Api.Controllers;

[ApiController]
[Route("backend/auth")]
public class AuthenticationController : BaseController
{
	private readonly IAuthService _authService;

	public AuthenticationController(IAuthService authService)
	{
		_authService = authService;
	}

	/// <summary>
	/// Log the user in. Note that passing plaintext password through unencrypted channel is insecure.
	/// </summary>
	/// <returns>User data and JWT token for authorization.</returns>
	/// <response code="400">On authentication failure.</response>
	/// <response code="500">On exception.</response>
	[HttpGet("login")]
	[ProducesResponseType(typeof(LogInResponse), StatusCodes.Status200OK)]
	[ProducesResponseType(StatusCodes.Status400BadRequest)]
	[ProducesResponseType(StatusCodes.Status500InternalServerError)]
	public async Task<IActionResult> LogInAsync([FromQuery] LoginModel model)
	{
		if(ModelState.IsValid is false)
		{
			return BadRequest("Invalid login credentials.");
		}
		
		var token = await _authService.Login(model);
		if (token is null)
		{
			return BadRequest("Invalid login credentials.");
		}

		return Ok(token);
	}

	/// <summary>
	/// Log the user out. This should invalidate current JWT's by advancing some kind of user
	/// bound counter that is also passed in JWT's and checked in authentication step.
	/// </summary>
	[HttpGet("logout")]
	public async Task LogOut()
	{
		await _authService.Logout();
	}
}
