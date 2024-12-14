namespace Ktu.T120B178.Api.Entities;

public class DemoEntityGroup : BaseEntity
{
	public string Name { get; set; } = null!;
    
	public ICollection<DemoEntity> DemoEntities { get; set; } = new List<DemoEntity>();
}