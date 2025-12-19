# Étape 1 : Build
FROM mcr.microsoft.com/dotnet/sdk:8.0 AS build
WORKDIR /app

# Copie tous les fichiers dans le conteneur
COPY . ./

# Restaure les dépendances et publie l'application
RUN dotnet restore
RUN dotnet publish -c Release -o out

# Étape 2 : Runtime
FROM mcr.microsoft.com/dotnet/aspnet:8.0 AS runtime
WORKDIR /app

# Copie les fichiers publiés depuis l'étape de build
COPY --from=build /app/out .

# Expose le port HTTP
EXPOSE 80

# Lance l'application
ENTRYPOINT ["dotnet", "Brasil Burger Client.dll"]
