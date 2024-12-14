using Ktu.T120B178.Api.Entities;
using Ktu.T120B178.Api.Models.DemoEntities;
using Ktu.T120B178.Api.Repositories;

namespace Ktu.T120B178.Api.Services.DemoEntities;

public sealed class DemoDemoEntityService : IDemoEntityService
{
	private readonly IDemoEntityRepository _repository;

	public DemoDemoEntityService(IDemoEntityRepository repository)
	{
		_repository = repository;
	}
	
	public async Task<int> CreateDemoEntity(CreateDemoEntityVm model, CancellationToken token = default)
	{
		var demoEntity = new DemoEntity
		{
			Name = model.Name,
			Condition = model.Condition,
			Date = model.Date,
			Deletable = model.Deletable
		};

		await _repository.AddAsync(demoEntity, token);
		await _repository.SaveChangesAsync(token);

		return demoEntity.Id;
	}
	
	public async Task<bool> UpdateDemoEntity(int id, UpdateDemoEntityVm model, CancellationToken token = default)
	{
		var entity = await _repository.FindAsync(id, token);

		if (entity is null)
		{
			return false;
		}
		
		Map(entity, model);
		
		await _repository.SaveChangesAsync(token);
		return true;
	}
	
	public async Task<bool> DeleteDemoEntity(int id, CancellationToken token = default)
	{
		var entity = await _repository.FindAsync(id, token);
		if (entity is null || !entity.Deletable)
		{
			return false;
		}
		
		
		await _repository.RemoveAsync(entity, token);
		await _repository.SaveChangesAsync(token);

		return true;
	}

	public async Task<IEnumerable<DemoEntityVm>> LoadDemoEntities(CancellationToken token = default)
	{
		var demoEntities = await _repository.GetAllAsync(token);
		return demoEntities.Select(Map);
	}

	public async Task<DemoEntityVm?> LoadDemoEntity(int id, CancellationToken token = default)
	{
		var demoEntity = await _repository.FindAsync(id, token);
		if (demoEntity is null)
		{
			return null;
		}
		
		return Map(demoEntity);
	}
    
	private static DemoEntityVm Map(DemoEntity demoEntity)
	{
		return new DemoEntityVm(
			demoEntity.Id, 
			demoEntity.Date, 
			demoEntity.Name, 
			demoEntity.Condition,
			demoEntity.Deletable);
	}
	
	private static void Map(DemoEntity entity, UpdateDemoEntityVm model)
	{
		entity.Name = model.Name;
		entity.Condition = model.Condition;
		entity.Deletable = model.Deletable;
		entity.Date = model.Date;
	}
}