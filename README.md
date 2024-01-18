# docker-boilerplate

This is a quick setup for containerising your project. It helps your install Mysql 5.7.22, phpmyadmin.

# Docker Compose Config

1. If you would use Mysql, update the Mysql Configuration;
   MYSQL_DATABASE: your_db
   MYSQL_USER: your_username
   MYSQL_PASSWORD: your_password
   MYSQL_ROOT_PASSWORD: your_root_password

2. Change the service_name and the service_name_queue to your preferred name in the docker-compose.yaml file;

3. All docker compose service should have different ports so ensure to change the port in the docker-compose file, specifically on line (13 & 38)

To use the Make command , go to the make file and make change to CONTAINER_PHP="your primary container" on line 4, the name should be the name you chose in step 2 above

# Make Commands

      1. To Start App
         make start

      2. to stop App
         make stop

      3. To rebuild and start the App
         make fresh

      4. To Destroy All contaner
         make destroy

      5. To restart container
         make restart

      7. to ssh into the parent container
         make ssh

      8. To run Migration on the parent container
         make migrate

      9. To run a fresh migration
         make migrate-fresh

      10. To run test
          make tests

      11. To run lint check
          make lint

      12. To fix lint issues
          make lint-fix

Cheers!!!!
