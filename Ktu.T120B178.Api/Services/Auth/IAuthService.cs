using Ktu.T120B178.Api.Models.Auth;

namespace Ktu.T120B178.Api.Services.Auth;

public interface IAuthService
{
	Task<LogInResponse?> Login(LoginModel model);
	Task Logout();
}