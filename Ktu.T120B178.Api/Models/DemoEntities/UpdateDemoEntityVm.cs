using System.ComponentModel.DataAnnotations;

namespace Ktu.T120B178.Api.Models.DemoEntities;

public sealed class UpdateDemoEntityVm
{
	/// <summary>
	/// Id. Required.
	/// </summary>
	[Required(ErrorMessage = "Is required.")]
	public int Id { get; init; }
	
	/// <summary>
	/// Date. Required.
	/// </summary>
	[Required(ErrorMessage = "Is required.")]
	public DateTime Date { get; init; }

	/// <summary>
	/// Name. Required.
	/// </summary>
	[Required(ErrorMessage = "Is required")]
	public string Name { get; init; } = null!;

	/// <summary>
	/// Condition. In range [0;10].
	/// </summary>
	[Range(0, 10, ErrorMessage = "Value should be between 0 and 10")]
	public int Condition { get; init; }

	/// <summary>
	/// Indicates if entity is deletable.
	/// </summary>
	public bool Deletable { get; init; }
}