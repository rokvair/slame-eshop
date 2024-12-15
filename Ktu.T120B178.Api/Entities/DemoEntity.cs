namespace Ktu.T120B178.Api.Entities;

public class DemoEntity : BaseEntity
{
    public DateTime Date { get; set; }
    
    public string Name { get; set; } = null!;
    
    public int Condition { get; set; }
    
    public bool Deletable { get; set; }
    
    public DemoEntityGroup? DemoEntityGroup { get; set; }
}