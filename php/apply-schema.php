<?php

$path = "/var/www/html";

$configFile = join(DIRECTORY_SEPARATOR, array($path, "config.php"));

echo "Configuring database for: " . $configFile . PHP_EOL;

# utils
function error($text)
{
    echo "Error: " . $text . PHP_EOL;
    exit(1);
}

function env($name, $default = null)
{
    $v = getenv($name) ?: $default;

    if ($v === null) {
        error("The env " . $name . " does not exist");
    }

    return $v;
}

function dbconnect($config)
{
    $options = array();

    foreach ($config as $key => $value) {
        if (null !== $value)
        {
            $options[] = $key . "=" . $value;
        }
    }

    $connectionString = "pgsql:" . join(";", $options);

    $pdo = new \PDO($connectionString);

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    return $pdo;

}

function dbcheck($config)
{
    try
    {
        dbconnect($config);
        return true;
    }
    catch (PDOException $e)
    {
        return false;
    }
}

// check whether user has provided DB options
if (env("DB_HOST", "") === "") {
    error("The env DB_HOST does not exist. Make sure to provide it");
}

// build config
$config = [
    "host" => env("DB_HOST"),
    "port" => env("DB_PORT"),
    "user" => env("DB_USERNAME"),
    "password" => env("DB_PASSWORD"),
    "dbname" => env("DB_NAME", "ttrss")
];

if (!dbcheck($config))
{
    echo "Database login failed, trying to create ..." . PHP_EOL;

    // superuser account to create new database and corresponding user account
    //   username (DB_SUPER_USER) can be supplied or defaults to "docker"
    //   password (DB_SUPER_PASSWORD) can be supplied or defaults to username
    // or if you don't want that, create a role and a password yourself

    $super = $config;

    $super["dbname"] = null;
    $super["user"] = env("DB_SUPER_USER", "docker");
    $super["password"] = env("DB_SUPER_PASSWORD", $super["user"]);

    $pdo = dbconnect($super);
    $pdo->exec("CREATE ROLE \"" . $config["user"] . "\" WITH LOGIN PASSWORD " . $pdo->quote($config["password"]));
    $pdo->exec("CREATE DATABASE \"" . $config["dbname"] . "\" WITH OWNER \"" . ($config["user"]) . "\"");

    if (dbcheck($config)) {
        echo "Database login created and confirmed" . PHP_EOL;
    } else {
        error("Database login failed, trying to create login failed as well");
    }
}

$pdo = dbconnect($config);

try {
    $pdo->query("SELECT 1 FROM ttrss_feeds");
    echo "Connection to database successful" . PHP_EOL;
    // reached this point => table found, assume db is complete
}
catch (PDOException $e) {
    echo "Database table not found, applying schema... " . PHP_EOL;

    $schema = file_get_contents(join(DIRECTORY_SEPARATOR, [
        $path,
        "schema/ttrss_schema_pgsql.sql"
    ]));

    $schema = preg_replace("/--(.*?);/", "", $schema);

    $schema = preg_replace("/[\r\n]/", " ", $schema);

    $schema = trim($schema, " ;");

    foreach (explode(";", $schema) as $stm) {
        $pdo->exec($stm);
    }
}
?>
