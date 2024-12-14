using Ktu.T120B178.Api.Data;
using Ktu.T120B178.Api.Entities;
using Ktu.T120B178.Api.Repositories.Common;

namespace Ktu.T120B178.Api.Repositories;

public sealed class DemoEntityRepository : BaseRepository<DemoEntity>, IDemoEntityRepository
{
    public DemoEntityRepository(ApplicationDbContext context) : base(context)
    {
    }
}