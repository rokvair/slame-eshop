using Ktu.T120B178.Api.Entities;
using Microsoft.EntityFrameworkCore;
using Microsoft.EntityFrameworkCore.Metadata.Builders;

namespace Ktu.T120B178.Api.Data.Configurations;

public class DemoEntityGroupConfiguration : IEntityTypeConfiguration<DemoEntityGroup>
{
	public void Configure(EntityTypeBuilder<DemoEntityGroup> builder)
	{
		builder.ToTable("DemoEntityGroups");

		builder.Property(deg => deg.Name)
			.IsRequired()
			.HasMaxLength(255);
	}
}