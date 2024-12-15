using System.Reflection;
using System.Text;
using System.Net;

using Ktu.T120B178.Api.Configurations;
using Microsoft.AspNetCore.Authentication.JwtBearer;
using Microsoft.IdentityModel.Tokens;
using Microsoft.OpenApi.Models;
using System.Text.Json.Serialization;

namespace Ktu.T120B178.Api;

/// <summary>
/// <para>Application entry point.</para>
/// <para>Static members are thread safe, instance members are not.</para>
/// </summary>
public class Program
{
	/// <summary>
	/// Program entry point.
	/// </summary>
	/// <param name="args">Command line arguments.</param>
	public static void Main(string[] args)
	{
		var self = new Program();
		self.Run(args);
	}

	/// <summary>
	/// Configures and runs the internals of the application.
	/// </summary>
	/// <param name="args">Command line arguments.</param>
	private void Run(string[] args)
	{
		var builder = WebApplication.CreateBuilder(args);

		//configure kestrel web server
		// builder.WebHost.ConfigureKestrel(opts => {
		// 	opts.Listen(IPAddress.Loopback, 5000);
		// });

		//
		builder.Services.AddProjectDependencies(builder.Configuration);

		//add and configure swagger documentation generator
		builder.Services.AddSwaggerGen(opts => {
			//include code comments in swagger documentation
			var xmlFilename = $"{Assembly.GetExecutingAssembly().GetName().Name}.xml";
			opts.IncludeXmlComments(Path.Combine(AppContext.BaseDirectory, xmlFilename));

			//enable JWT authentication support in swagger interface
			opts.AddSecurityDefinition(
				"JWT",
				new OpenApiSecurityScheme {
					Description = "JWT Authorization header using the Bearer scheme.",
					Name = "Authorization",
					In = ParameterLocation.Header,
					Type = SecuritySchemeType.Http,
					Scheme = "bearer"
				}
			);

			opts.AddSecurityRequirement(new OpenApiSecurityRequirement {
				{
					new OpenApiSecurityScheme {
						Reference = new OpenApiReference {
							Type = ReferenceType.SecurityScheme,
							Id = "JWT"
						}
					},
					new List<string>()
				}
			});
		});

		//turn on support for web api controllers
		builder.Services
			.AddControllers()
			.AddJsonOptions(opts => {
				//this makes enumerations values to be strings in OpenAPI docs
				opts.JsonSerializerOptions.Converters.Add(new JsonStringEnumConverter());
			});

		//configure JWT based authentication
		builder.Services
			.AddAuthentication(JwtBearerDefaults.AuthenticationScheme)
			.AddJwtBearer(opts => {
				opts.SaveToken = true;
				opts.TokenValidationParameters =
					//XXX: this is unsafe, use more restrictive settings once it works
					new TokenValidationParameters()	{
						ValidateIssuer = false,
						ValidateAudience = false,
						ValidAudience = "",
						ValidIssuer = "",
						IssuerSigningKey = new SymmetricSecurityKey(Encoding.UTF8.GetBytes(Config.JwtSecret))
					};
			});

		//add CORS policies
		builder.Services.AddCors(cr => {
			//allow everything from everywhere
			cr.AddPolicy("allowAll", cp => {
				cp.AllowAnyOrigin();
				cp.AllowAnyMethod();
				cp.AllowAnyHeader();
			});
		});

		//build the app
		var app = builder.Build();

		//turn CORS policy on
		app.UseCors("allowAll");

		//turn on support for swagger web page
		app.UseSwagger();
		app.UseSwaggerUI();

		//turn on request routing
		app.UseRouting();

		//these two lines turn on support for authentication and authorization middleware
		app.UseAuthentication();
		app.UseAuthorization();

		//configure routes
		app.UseEndpoints(ep => {
			ep.MapControllerRoute(
				name: "default",
				pattern: "{controller}/{action}/{id?}"
			);
		});

		//start the server, block until it shuts down
		app.Run();
	}
}
