<?php

namespace App\Repository;

interface ApprenantsFirebaseRepositoryInterface
{
    public function create(array $data);

    public function update(string $id, array $data);

    public function delete(string $id);

    public function find(string $id);

    public function findApprenantById(string $id);


    public function getAll();

    public function findUserByEmail(string $email);

    public function findUserByPhone(string $telephone);
    
    public function createUserWithEmailAndPassword($email,$password);

    public function uploadImageToStorage($filePath, $fileName);

}
