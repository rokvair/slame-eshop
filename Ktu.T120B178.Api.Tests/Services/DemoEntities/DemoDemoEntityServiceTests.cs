using Ktu.T120B178.Api.Entities;
using Ktu.T120B178.Api.Repositories;
using Ktu.T120B178.Api.Services.DemoEntities;

namespace Ktu.T120B178.Api.Tests.Services.DemoEntities;

public class DemoDemoEntityServiceTests
{
    private Mock<IDemoEntityRepository> _repositoryMock;
    private DemoDemoEntityService _demoEntityService;
    
    [SetUp]
    public void Setup()
    {
        // Initialize the mock
        _repositoryMock = new Mock<IDemoEntityRepository>();
        
        // Pass the mock object to the class being tested
        _demoEntityService = new DemoDemoEntityService(_repositoryMock.Object);
    }
    
    [Test]
    public async Task GetDemoEntityById_ShouldReturnEntity_WhenEntityExists()
    {
        // Arrange
        var existingEntity = new DemoEntity { Id = 1, Name = "Existing Entity" };
        _repositoryMock
            .Setup(x => x.FindAsync(1, It.IsAny<CancellationToken>()))
            .ReturnsAsync(existingEntity);

        // Act
        var result = await _demoEntityService.LoadDemoEntity(1, CancellationToken.None);

        // Assert
        Assert.That(result, Is.Not.Null);
        Assert.Multiple(() =>
        {
            Assert.That(result.id, Is.EqualTo(1));
            Assert.That(result.name, Is.EqualTo("Existing Entity"));
        });
    }

    [Test]
    public async Task GetDemoEntityById_ShouldReturnNull_WhenEntityDoesNotExist()
    {
        // Arrange
        _repositoryMock
            .Setup(x => x.FindAsync(It.IsAny<int>(), It.IsAny<CancellationToken>()))
            .ReturnsAsync((DemoEntity?)null);

        // Act
        var result = await _demoEntityService.LoadDemoEntity(999, CancellationToken.None);

        // Assert
        Assert.That(result, Is.Null);
    }
}