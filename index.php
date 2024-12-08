<?php
declare(strict_types=1);

class Person {
    private string $name;
    private int $age;
    private string $login;

    public function __construct(string $name, int $age, string $login) {
        $this->name = $name;
        $this->age = $age;
        $this->login = $login;
    }

    public function __get(string $property) {
        if (property_exists($this, $property)) {
            return $this->$property;
        }
        return null;
    }

    public function __set(string $property, $value): void {
        if (property_exists($this, $property)) {
            $this->$property = $value;
        }
    }

    public function __sleep(): array {
        return ['name', 'age', 'login'];
    }

    public function __wakeup() {
        if ($this->age < 0) {
            throw new Exception("Возраст не может быть отрицательным");
        }
        error_log("Объект Person с логином '{$this->login}' был десериализован.");

    }

    public function __toString(): string {
        return "Name: {$this->name}, Age: {$this->age}, Login: {$this->login}";
    }
}


class PeopleList implements Iterator {
    private array $people = [];
    private int $position = 0;

    public function addPerson(Person $person): void {
        $this->people[] = $person;
    }

    public function current(): Person {
        return $this->people[$this->position];
    }

    public function key(): int {
        return $this->position;
    }

    public function next(): void {
        ++$this->position;
    }

    public function rewind(): void {
        $this->position = 0;
    }

    public function valid(): bool {
        return isset($this->people[$this->position]);
    }
}

$person1 = new Person("Alice", 30, "alice123");
$person2 = new Person("Bob", 25, "bob456");
$serializedPerson = serialize($person1);
echo "Сериализованный объект:\n" . $serializedPerson . "\n";
$modifiedSerializedPerson = str_replace("alice123", "alicenew", $serializedPerson);
$deserializedPerson = unserialize($modifiedSerializedPerson);
echo "Десериализованный объект:\n" . $deserializedPerson  . "\n";

$peopleList = new PeopleList();
$peopleList->addPerson($person1);
$peopleList->addPerson($person2);

echo "Список людей:\n";
foreach ($peopleList as $person) {
    echo $person . "\n";
}