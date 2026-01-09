# weather_php

Weather Dashboard – Live API & Database Driven

A single-page PHP web application that fetches live weather data from an external API, stores selected records in a database, and visualizes real-time temperature trends for cities stored in the database using Chart.js.

This project was built as part of a technical assignment to demonstrate API integration, CRUD operations, data visualization, and clean architecture.

🚀 Features
🌍 Weather API Integration

Fetches live weather data (temperature, humidity, condition) for any city

Uses a server-side PHP API layer

💾 Database-Driven City Management

Cities are stored only when the user clicks Save

Database acts as the source of truth for tracked cities

📈 Live Temperature Chart

Chart displays live temperature values from the API

Each city stored in the database is fetched live

Each city is represented by a unique colored line

Chart updates automatically every 10 seconds

🎯 Interactive Chart Highlighting

Clicking a city row highlights only that city’s line

Other city lines fade for better focus

🧮 CRUD Operations (Mandatory)

Create – Save weather data for a city

Read – View saved records with pagination

Update – Edit temperature and humidity using a modal popup

Delete – Remove records from the database

🏆 Leaderboard

Displays cities with the highest recorded temperatures
