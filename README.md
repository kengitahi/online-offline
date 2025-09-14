# Online/Offline App

## About Online/Offline

This is a simple app that saves data to MySQL when the user is online and to sqlite when they are offline. If they save data when offline, the data is automatically synced to the MySQL database when they get back online.

The app also features custom toast messages that alert the user of their connection status when they open the page. 

These messages also alert them when they go offline and came back online, as well as when data is being synced, after synchronization is complete, and when there are no posts to sysnc if they were offline and came back online.

## Tech Stack
- Laravel
- Tailwind CSS
- Livewire backend validation and post creation
- Alpine.js for network status detection and creating the toast messages

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

## Learning Livewire and Alpine JS

Livewire also has extensive [documentation](https://livewire.laravel.com/docs/quickstart) that you can use to get started and build full apps.

You can access the Alpine.js cocumentation [here](https://alpinejs.dev/start-here) to learn it.

