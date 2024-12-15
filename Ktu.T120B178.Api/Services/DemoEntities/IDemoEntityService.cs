using Ktu.T120B178.Api.Models.DemoEntities;

namespace Ktu.T120B178.Api.Services.DemoEntities;

public interface IDemoEntityService
{
	Task<IEnumerable<DemoEntityVm>> LoadDemoEntities(CancellationToken token = default);
	
	Task<DemoEntityVm?> LoadDemoEntity(int id, CancellationToken token = default);

	Task<int> CreateDemoEntity(CreateDemoEntityVm model, CancellationToken token = default);
	
	Task<bool> UpdateDemoEntity(int id, UpdateDemoEntityVm model, CancellationToken token = default);

	Task<bool> DeleteDemoEntity(int id, CancellationToken token = default);
}