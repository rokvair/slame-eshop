using System;
using Microsoft.EntityFrameworkCore.Metadata;
using Microsoft.EntityFrameworkCore.Migrations;

#nullable disable

namespace Ktu.T120B178.Api.Migrations
{
    public partial class InitialMigration : Migration
    {
        protected override void Up(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.AlterDatabase()
                .Annotation("MySql:CharSet", "utf8mb4");

            migrationBuilder.CreateTable(
                name: "DemoEntities",
                columns: table => new
                {
                    Id = table.Column<int>(type: "int", nullable: false)
                        .Annotation("MySql:ValueGenerationStrategy", MySqlValueGenerationStrategy.IdentityColumn),
                    Date = table.Column<DateTime>(type: "datetime(6)", nullable: false),
                    Name = table.Column<string>(type: "varchar(255)", maxLength: 255, nullable: false)
                        .Annotation("MySql:CharSet", "utf8mb4"),
                    Condition = table.Column<int>(type: "int", nullable: false),
                    Deletable = table.Column<bool>(type: "tinyint(1)", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_DemoEntities", x => x.Id);
                })
                .Annotation("MySql:CharSet", "utf8mb4");
            
            //Seed data
            Random random = new Random();
            for (int i = 1; i <= 30; i++)
            {
                migrationBuilder.InsertData(
                    table: "DemoEntities",
                    columns: new[] { "Id", "Date", "Name", "Condition", "Deletable" },
                    values: new object[] { i, DateTime.Now.Date.AddDays(-60 + i), $"Demo entity {i}", random.Next(0, 11), i % 2 == 0 });
            }
        }

        protected override void Down(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.DropTable(
                name: "DemoEntities");
        }
    }
}
