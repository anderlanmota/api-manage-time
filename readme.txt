Backend API RESTful to manage time consumed in projects.

API Version: V1
Programming language: PHP (Version 8.0.14)
Database: MySQL (Version 5.7.36)

Api Structure:
api/
├─ core/
│    ├─ [ main classes and config ]
└─ model/
│    ├─ [ classes responsible for resource functionality ]
└─ resource/
│    ├─ v1.php [ resource PHP files. call the required model class (in the model folder) ]
│ autoload.php [ allows automatic inclusion of class files ]
│ index.php [ main file ]

Installation:
1. edit the file /api/core/Database.php
2. import the file database.db in the database
3. edit the file /api/core/Email.php
4. Use the api to edit the admin user (login: admin / password: admin)

Ready! The api can now be used :)

Features:

/auth
 (banco de dados tb_keys, usa pra sessao e alterar email)


/users
 (banco de dados tb_users)


/projects
 (tb_projects, tb_projects_users)


/times
 (tb_times)



r4s07dx30s0o

Usuário: anderlan_vibbra

Banco de dados: anderlan_vibbra


