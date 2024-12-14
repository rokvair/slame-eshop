using Ktu.T120B178.Api.Data;
using Ktu.T120B178.Api.Repositories;
using Ktu.T120B178.Api.Services.Auth;
using Ktu.T120B178.Api.Services.DemoEntities;
using Microsoft.EntityFrameworkCore;

namespace Ktu.T120B178.Api.Configurations;

public static class DependencyInjection
{
	public static IServiceCollection AddProjectDependencies(
		this IServiceCollection services, 
		IConfiguration configuration)
	{
		services.AddDbContext<ApplicationDbContext>((_, builder) =>
		{
		    var connectionString = configuration.GetConnectionString("DatabaseConnection");
		    builder.UseMySql(connectionString, ServerVersion.AutoDetect(connectionString));
		});
		
		services.AddScoped<IAuthService, AuthService>();
		services.AddScoped<IDemoEntityRepository, DemoEntityRepository>();
		services.AddScoped<IDemoEntityService, DemoDemoEntityService>();
        
		return services;
	}
}