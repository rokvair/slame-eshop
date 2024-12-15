using System.ComponentModel.DataAnnotations;

namespace Ktu.T120B178.Api.Models.Auth;

public class LoginModel
{
	[Required]
	public string UserName { get; set; } = null!;
	
	[Required]
	public string Password { get; set; } = null!;
}