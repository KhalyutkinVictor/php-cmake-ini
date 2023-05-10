# Simple cmake initializer

## Quick start

- Clone repository

  ```
  git clone https://github.com/KhalyutkinVictor/php-cmake-ini.git
  ```

- Add cmake-ini to /usr/bin

  ```
  sudo ln -s <full path to>/cmake-ini /usr/bin/cmake-ini
  ```
  
## Start using

- Initialize new cmake project at current directory

  ```
  cmake-ini --name=<project name>
  ```
  
- Init new project at directory /my/best/proj

  ```
  cmake-ini --name=<project name> --out=/my/best/proj
  ```

- Init c++ 11 project at current directory

  ```
  cmake-ini --name=<project name> --lang=CXX --stndrt=11
  ```
  
- Init project and generate compile_commands.json (immidiatly run ``` cmake -DCMAKE_EXPORT_COMPILE_COMMANDS=On ```)

  ```
  cmake-ini --name=<project name> --exportcc=yes
  ```
