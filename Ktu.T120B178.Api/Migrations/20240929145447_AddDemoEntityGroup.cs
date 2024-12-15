using Microsoft.EntityFrameworkCore.Metadata;
using Microsoft.EntityFrameworkCore.Migrations;

#nullable disable

namespace Ktu.T120B178.Api.Migrations
{
    /// <inheritdoc />
    public partial class AddDemoEntityGroup : Migration
    {
        /// <inheritdoc />
        protected override void Up(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.AlterColumn<int>(
                name: "Id",
                table: "DemoEntities",
                type: "int",
                nullable: false,
                oldClrType: typeof(int),
                oldType: "int")
                .Annotation("MySql:ValueGenerationStrategy", MySqlValueGenerationStrategy.IdentityColumn);

            migrationBuilder.AddColumn<int>(
                name: "DemoEntityGroupId",
                table: "DemoEntities",
                type: "int",
                nullable: true);

            migrationBuilder.CreateTable(
                name: "DemoEntityGroups",
                columns: table => new
                {
                    Id = table.Column<int>(type: "int", nullable: false)
                        .Annotation("MySql:ValueGenerationStrategy", MySqlValueGenerationStrategy.IdentityColumn),
                    Name = table.Column<string>(type: "varchar(255)", maxLength: 255, nullable: false)
                        .Annotation("MySql:CharSet", "utf8mb4")
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_DemoEntityGroups", x => x.Id);
                })
                .Annotation("MySql:CharSet", "utf8mb4");

            migrationBuilder.CreateIndex(
                name: "IX_DemoEntities_DemoEntityGroupId",
                table: "DemoEntities",
                column: "DemoEntityGroupId");

            migrationBuilder.AddForeignKey(
                name: "FK_DemoEntities_DemoEntityGroups_DemoEntityGroupId",
                table: "DemoEntities",
                column: "DemoEntityGroupId",
                principalTable: "DemoEntityGroups",
                principalColumn: "Id",
                onDelete: ReferentialAction.Cascade);
            
            //create sample data
            Seed(migrationBuilder);
        }
        
        private void Seed(MigrationBuilder migrationBuilder)
        {
            // Add two groups
            migrationBuilder.InsertData(
                table: "DemoEntityGroups",
                columns: new[] { "Id", "Name" },
                values: new object[,]
                {
                    { 1, "Even Group" },
                    { 2, "Odd Group" }
                });

            // Update the existing DemoEntity records to associate with the correct group
            for (int id = 1; id <= 30; id++)
            {
                int groupId = (id % 2 == 0) ? 1 : 2; // Even Ids -> Group 1, Odd Ids -> Group 2

                migrationBuilder.UpdateData(
                    table: "DemoEntities",
                    keyColumn: "Id",
                    keyValue: id,
                    column: "DemoEntityGroupId",
                    value: groupId);
            }
        }

        /// <inheritdoc />
        protected override void Down(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.DropForeignKey(
                name: "FK_DemoEntities_DemoEntityGroups_DemoEntityGroupId",
                table: "DemoEntities");

            migrationBuilder.DropTable(
                name: "DemoEntityGroups");

            migrationBuilder.DropIndex(
                name: "IX_DemoEntities_DemoEntityGroupId",
                table: "DemoEntities");

            migrationBuilder.DropColumn(
                name: "DemoEntityGroupId",
                table: "DemoEntities");

            migrationBuilder.AlterColumn<int>(
                name: "Id",
                table: "DemoEntities",
                type: "int",
                nullable: false,
                oldClrType: typeof(int),
                oldType: "int")
                .OldAnnotation("MySql:ValueGenerationStrategy", MySqlValueGenerationStrategy.IdentityColumn);
        }
    }
}
