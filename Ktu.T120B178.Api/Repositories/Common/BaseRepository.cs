using Ktu.T120B178.Api.Data;
using Ktu.T120B178.Api.Entities;
using Microsoft.EntityFrameworkCore;

namespace Ktu.T120B178.Api.Repositories.Common;

public abstract class BaseRepository<T> : IRepository<T> where T : BaseEntity
{
    private readonly ApplicationDbContext _context;

    protected BaseRepository(ApplicationDbContext context)
    {
        _context = context;
    }
    
    protected ApplicationDbContext Context => _context;

    public virtual async Task AddAsync(T entity, CancellationToken token = default)
    {
        await _context
            .Set<T>()
            .AddAsync(entity, token);
    }

    public virtual async Task<IEnumerable<T>> GetAllAsync(CancellationToken token = default)
    {
        return await _context
            .Set<T>()
            .ToListAsync(token);
    }

    public virtual Task RemoveAsync(T entity, CancellationToken token = default)
    {
        _context
            .Set<T>()
            .Remove(entity);
        
        return Task.CompletedTask;
    }

    public virtual async Task<T?> FindAsync(int id, CancellationToken token = default)
    {
        return await _context
            .Set<T>()
            .FindAsync(
                keyValues: new object[] { id }, 
                cancellationToken: token);
    }
    
    public virtual async Task SaveChangesAsync(CancellationToken token = default)
    {
        await _context.SaveChangesAsync(token);
    }
}