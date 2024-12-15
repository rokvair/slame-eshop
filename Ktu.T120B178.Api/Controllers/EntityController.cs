using Ktu.T120B178.Api.Common;
using Ktu.T120B178.Api.Models.DemoEntities;
using Ktu.T120B178.Api.Services.DemoEntities;
using Microsoft.AspNetCore.Authorization;
using DemoEntityVm = Ktu.T120B178.Api.Models.DemoEntities.DemoEntityVm;

namespace Ktu.T120B178.Api.Controllers;

/// <summary>
/// <para>Implements restfull API for working with entities</para>
/// <para>Thread safe.</para>
/// </summary>
[ApiController]
[Route("backend/entity")]
public class EntityController : BaseController
{
	private readonly IDemoEntityService _demoEntityService;

	public EntityController(IDemoEntityService demoEntityService)
	{
		_demoEntityService = demoEntityService;
	}

	/// <summary>
	/// List entities.
	/// </summary>
	/// <returns>A list of entities.</returns>
	/// <response code="500">On exception.</response>
	[HttpGet("list")]
	[Authorize(Roles = "user")]
	[ProducesResponseType(typeof(List<DemoEntityVm>), StatusCodes.Status200OK)]
	[ProducesResponseType(StatusCodes.Status500InternalServerError)]
	public async Task<IActionResult> ListAsync()
	{
		//load entities from DB, convert into listing views
		var result = await _demoEntityService.LoadDemoEntities();
		return Ok(result);
	}

	/// <summary>
	/// Loads data for a single entity.
	/// </summary>
	/// <returns>Data of entity loaded.</returns>
	/// <response code="404">If entity with given ID can't be loaded.</response>
	/// <response code="500">On exception.</response>
	[HttpGet("load")]
	[Authorize(Roles = "user")]
	[ProducesResponseType(typeof(DemoEntityVm), StatusCodes.Status200OK)]
	[ProducesResponseType(StatusCodes.Status404NotFound)]
	[ProducesResponseType(StatusCodes.Status500InternalServerError)]
	public async Task<IActionResult> LoadAsync([FromQuery] int id, CancellationToken token = default)
	{
		//load entity from DB, convert into create/update view
		var demoEntity = await _demoEntityService.LoadDemoEntity(id, token);

		//entity not found?
		if (demoEntity is null)
		{
			return NotFound();
		}

		return Ok(demoEntity);
	}

	/// <summary>
	/// Creates new entity.
	/// </summary>
	/// <returns>ID of new entity</returns>
	/// <response code="400">On validation failure.</response>
	/// <response code="500">On exception.</response>
	[HttpPost("create")]
	[Authorize(Roles = "user")]
	[ProducesResponseType(typeof(int), StatusCodes.Status200OK)]
	[ProducesResponseType(StatusCodes.Status400BadRequest)]
	[ProducesResponseType(StatusCodes.Status500InternalServerError)]
	public async Task<IActionResult> CreateAsync(CreateDemoEntityVm model, CancellationToken cancellationToken)
	{
		if (!ModelState.IsValid)
		{
			return BadRequest(ModelState);
		}
		
		//save to DB
		var result = await _demoEntityService.CreateDemoEntity(model, cancellationToken);

		return Ok(result);
	}

	/// <summary>
	/// Updates given entity.
	/// </summary>
	/// <returns>ID of new entity</returns>
	/// <response code="400">On validation failure.</response>
	/// <response code="500">On exception.</response>
	[HttpPost("update")]
	[Authorize(Roles = "user")]
	[ProducesResponseType(StatusCodes.Status200OK)]
	[ProducesResponseType(StatusCodes.Status400BadRequest)]
	[ProducesResponseType(StatusCodes.Status500InternalServerError)]
	public async Task<IActionResult> UpdateAsync(UpdateDemoEntityVm model, CancellationToken cancellationToken)
	{
		if (!ModelState.IsValid)
		{
			return BadRequest(ModelState);
		}

		var completed = await _demoEntityService.UpdateDemoEntity(model.Id, model, cancellationToken);

		if (!completed)
		{
			return NotFound();
		}

		return Ok();
	}

	/// <summary>
	/// Deletes given entity.
	/// </summary>
	/// <response code="404">If entity with given ID can't be found.</response>
	/// <response code="400">If entity with given ID is not marked as deletable.</response>
	/// <response code="500">On exception.</response>
	[HttpGet("delete")]
	[Authorize(Roles = "user")]
	[ProducesResponseType(StatusCodes.Status200OK)]
	[ProducesResponseType(StatusCodes.Status404NotFound)]
	[ProducesResponseType(StatusCodes.Status400BadRequest)]
	[ProducesResponseType(StatusCodes.Status500InternalServerError)]
	public async Task<IActionResult> DeleteAsync([FromQuery] int id, CancellationToken token = default)
	{
		var completed = await _demoEntityService.DeleteDemoEntity(id, token);

		if (!completed)
		{
			return BadRequest();
		}

		return Ok();
	}
}
