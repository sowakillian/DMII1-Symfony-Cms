# Gobooking

Gobooking is a Symfony web application created to improve equipment booking in the school "Les Gobelins". 

## Get the project

To get the project in locale, 
- Clone the project with the command

```
git clone https://github.com/sowakillian/DMII1-Symfony-GoBooking.git
```

## Launch the project

- Update the composer modules

```
composer update
```

- Update the doctrine schema
```
php bin/console doctrine:schema:update
```

- Launch the server
```
symfony server:start
```

- Launch webpack encore for the assets
```bash
yarn encore dev --watch
```

/!\ Administration is opened to all the users in development mode, don't forget to create admin account before passing in production


## Features

- Registration and connection
- Translation of all the application
- Translated routes
- Slug for users specific routes
- Booking creation , edition, deletion
- Booking status management with Workflow
- Category creation, edition, deletion
- Equipment creation, edition, deletion
- Equipment image upload system
- Users creation, edition, deletion
- API created with API Platform
- Constraints for the forms security


## Contributing
Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

Please make sure to update tests as appropriate.

## License
[MIT](https://choosealicense.com/licenses/mit/)
