<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[WebReinvent](https://webreinvent.com/)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Jump24](https://jump24.co.uk)**
- **[Redberry](https://redberry.international/laravel/)**
- **[Active Logic](https://activelogic.com)**
- **[byte5](https://byte5.de)**
- **[OP.GG](https://op.gg)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).



Challenge Description:

You are given the challenge to create a Laravel/Vue application that manages a task list. The application should allow users to create tasks, mark them as complete, and delete tasks. Your goal is to enhance the application by implementing the following features and writing tests to ensure their functionality.

Features to Implement:

Task Filtering: Add the ability to filter tasks based on their status (complete/incomplete) using Vue components. When a filter option is selected, the task list should update dynamically to display only the relevant tasks. Write appropriate tests to verify the filtering behaviour.

Task Sorting: Implement the ability to sort tasks based on their creation date, either in ascending or descending order. Add sorting options to the Vue components and ensure that the tasks are correctly sorted on the frontend. Write tests to validate the sorting functionality.

Private/Public Tasks: Extend the task model and database schema to include a flag indicating whether a task is private or public. Private tasks should only be visible to the owner, while public tasks can be viewed by anyone with the share link.

Share Link: Implement the ability to generate a shareable link for public tasks. When a task is marked as public, generate a unique share link that can be accessed by anyone. The share link should display the task details but not allow any modifications. Ensure that the share link is properly formatted and easily shareable.

Unit Testing: Write comprehensive unit tests to cover the existing and newly implemented functionality. Ensure that all critical code paths are tested and that the tests are focused on both frontend (Vue components) and backend (Laravel) components. Use appropriate testing frameworks and tools (e.g., PHPUnit, Laravel Dusk, Jest) for each layer.

Optional (NoSQL Integration):

If you choose to integrate a NoSQL database (e.g., MongoDB), you can do so as an additional task. Modify the existing Eloquent models and migrations to use NoSQL instead of traditional SQL. Update the database operations (create, read, update, delete) to work with the NoSQL database. Write tests to confirm the correct integration and functionality of the NoSQL database. If you decide not to integrate NoSQL, ensure that your code remains compatible with the existing SQL database.
Instructions:

Fork the provided Bitbucket repository to your own account.
Implement the required features and write corresponding tests.
Commit your changes to your forked repository.
Once finished, share the link to your forked repository with the hiring team for review: (BitBucket: n.costa@landbellgroup.com; t.hessel@landbellgroup.com)
Evaluation Criteria:

Implementation of the requested features.
Code quality, including adherence to Laravel/Vue best practices, maintainability, and readability.
Test coverage and correctness of the implemented tests.
Proper usage of Vue components for task filtering, sorting, and share link functionality.
Implementation of private/public tasks and the ability to generate share links for public tasks.
Optional: Proper integration of the NoSQL database (if chosen) and validation of data operations.
Configuration of code coverage and CI/CD pipeline.
How to apply
Must be done by GitHub, GitLab or BitBucket (recommended)
When finished, send the link or ask to add members (BitBucket: n.costa@landbellgroup.com; t.hessel@landbellgroup.com)
Note: Feel free to make any necessary improvements to the existing codebase, such as refactoring, optimizing queries, or enhancing the user interface.w