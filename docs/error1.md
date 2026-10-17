SQLSTATE[08S01]: [Microsoft][ODBC Driver 18 for SQL Server]TCP Provider: Error code 0x2746 (Connection: sqlsrv, Host: ProductManagementDbEy.mssql.somee.com, Port: 1433, Database: ProductManagementDbEy, SQL: insert into [people] ([name], [document_type], [document_number], [email], [current_pets_count], [estado_civil], [cantidad_hijos], [occupation_id], [residence_id], [income], [updated_at], [created_at]) values (D. Guillermo Esparza Segundo, DPI, 1661947652920, heredia.guillermo@example.com, 3, Unión libre, 2, 10, 6, 6686, 2026-10-15 18:46:33.385, 2026-10-15 18:46:33.385))

  at vendor/laravel/framework/src/Illuminate/Database/Connection.php:857
    853▕             $exceptionType = ($isUniqueConstraintError = $this->isUniqueConstraintError($e))
    854▕                 ? UniqueConstraintViolationException::class
    855▕                 : QueryException::class;
    856▕ 
  ➜ 857▕             $exception = new $exceptionType(
    858▕                 $this->getNameWithReadWriteType(),
    859▕                 $query,
    860▕                 $this->prepareBindings($bindings),
    861▕                 $e,

      +18 vendor frames 

  19  database/seeders/PersonSeeder.php:25
      Illuminate\Database\Eloquent\Model::__callStatic("create")
      +8 vendor frames 

  28  database/seeders/DatabaseSeeder.php:17
      Illuminate\Database\Seeder::call("Database\Seeders\PersonSeeder")