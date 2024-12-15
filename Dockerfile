#See https://aka.ms/containerfastmode to understand how Visual Studio uses this Dockerfile to build your images for faster debugging.

FROM mcr.microsoft.com/dotnet/aspnet:6.0 AS base
WORKDIR /app
EXPOSE 80
EXPOSE 443

FROM mcr.microsoft.com/dotnet/sdk:6.0 AS build
WORKDIR /src
COPY ["Ktu.T120B178.Api/Ktu.T120B178.Api.csproj", "Ktu.T120B178.Api/"]
COPY ["Ktu.T120B178.Application/Ktu.T120B178.Application.csproj", "Ktu.T120B178.Application/"]
COPY ["Ktu.T120B178.Domain/Ktu.T120B178.Domain.csproj", "Ktu.T120B178.Domain/"]
COPY ["Ktu.T120B178.Infrastructure/Ktu.T120B178.Infrastructure.csproj", "Ktu.T120B178.Infrastructure/"]
RUN dotnet restore "Ktu.T120B178.Api/Ktu.T120B178.Api.csproj"
COPY . .
WORKDIR "/src/Ktu.T120B178.Api"
RUN dotnet build "Ktu.T120B178.Api.csproj" -c Release -o /app/build

FROM build AS publish
RUN dotnet publish "Ktu.T120B178.Api.csproj" -c Release -o /app/publish /p:UseAppHost=false

FROM base AS final
WORKDIR /app
COPY --from=publish /app/publish .
ENTRYPOINT ["dotnet", "Ktu.T120B178.Api.dll"]

