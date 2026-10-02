<?php

use App\Business\Entities\AnimalEntity;

test('Puede ser creado con todas las propiedades', function () {
    $data = [
        'id' => 1,
        'habitat_id' => 5,
        'species_id' => 10,
        'code' => 'LEO-01',
        'name' => 'Simba',
        'sex' => 'M',
        'birth_date' => '2020-05-12',
        'admission_date' => '2023-01-10',
        'health_status' => 'quarantine'
    ];

    $animal = AnimalEntity::fromArray($data);

    $this->assertEquals('Simba', $animal->name);
    $this->assertEquals(5, $animal->habitatId);
    $this->assertEquals('quarantine', $animal->healthStatus);
});

test('Code normalizado', function () {
    $data = [
        'habitat_id' => 5,
        'species_id' => 10,
        'code' => ' qwerty-123  ',
        'name' => 'Simba',
    ];

    $animal = AnimalEntity::fromArray($data);

    $this->assertEquals('QWERTY-123', $animal->code);
});

test('Asigna los valores por defecto correctamente', function () {
    $data = [
        'id' => 1,
        'habitat_id' => 5,
        'species_id' => 10,
        'code' => ' LEO-01 ',
        'name' => 'Simba',
    ];

    $animal = AnimalEntity::fromArray($data);

    $this->assertEquals('unknown', $animal->sex);
    $this->assertEmpty($animal->birthDate);
    $this->assertEquals(date('Y-m-d'), $animal->admissionDate);
    $this->assertEquals('healthy', $animal->healthStatus);
});
