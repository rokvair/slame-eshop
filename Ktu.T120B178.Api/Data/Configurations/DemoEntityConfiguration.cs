using Ktu.T120B178.Api.Entities;
using Microsoft.EntityFrameworkCore;
using Microsoft.EntityFrameworkCore.Metadata.Builders;

namespace Ktu.T120B178.Api.Data.Configurations;

public class DemoEntityConfiguration : IEntityTypeConfiguration<DemoEntity>
{
    public void Configure(EntityTypeBuilder<DemoEntity> builder)
    {
        builder.ToTable("DemoEntities");
        
        builder.HasKey(p => p.Id);
        
        builder.Property(p => p.Date)
            .IsRequired();
        
        builder.Property(p => p.Name)
            .HasMaxLength(255)
            .IsRequired();

        builder.Property(p => p.Condition)
            .IsRequired();
        
        builder.Property(p => p.Deletable)
            .IsRequired();

        builder.HasOne(de => de.DemoEntityGroup)
            .WithMany(deg => deg.DemoEntities)
            .OnDelete(DeleteBehavior.Cascade);
    }
}