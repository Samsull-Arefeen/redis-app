**Redis App Assignment**

I've kept this project as simple as possible. I've also commited the vendor directory, so it'll be a plug and play after you checkout the project.
Please set databse and redis credentials in _database.php_ and _.env_ files.

The porject uses very basic design using bootstrap from cdn.

There is a migration script. After you run the project got to url -- http://localhost:8000/persons/migrate
I've altered the table and added 2 extra columns during migration (for better query execution/performance).

**Please note the, the migration takes 4-5 minutes to insert all 100000 data. And the "persons" page won't work properly, if the migration script isn't executed successfully.**

After successfull migration, you'll be able to get the page from this url -- http://localhost:8000/persons .
