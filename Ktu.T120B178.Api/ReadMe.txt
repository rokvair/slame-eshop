Duomenų bazės konfiguravimas (migracijų paleidimas):

1. paleidžiame DBVS;
2. atidarome terminalo langą(powershell), kelias: <kelias_iki_projekto>/dotnet;
3. jei norite pridėti migraciją pavadinimu "InitialMigration":
dotnet ef migrations add InitialMigration --project ./Ktu.T120B178.Api/Ktu.T120B178.Api.csproj
4. migracijų sudėjimas į duomenų bazę:
dotnet ef database update --project ./Ktu.T120B178.Api/Ktu.T120B178.Api.csproj
