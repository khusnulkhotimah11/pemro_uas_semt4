<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;

class Barang extends ResourceController
{
    protected $modelName = 'App\Models\Barang';
    protected $format    = 'json';

    // GET /api/barang
    public function index()
    {
        return $this->respond($this->model->findAll());
    }

    // POST /api/barang (Tambah Data)
    public function create()
    {
        $data = $this->request->getJSON(true);
        if ($this->model->insert($data)) {
            return $this->respondCreated(['message' => 'Barang sukses ditambahkan']);
        }
        return $this->fail('Gagal menambah data');
    }

    // PUT /api/barang/{id} (Update Data)
    public function update($id = null)
    {
        $data = $this->request->getJSON(true);
        if ($this->model->update($id, $data)) {
            return $this->respond(['message' => 'Barang sukses diupdate']);
        }
        return $this->fail('Gagal update data');
    }

    // DELETE /api/barang/{id} (Hapus Data)
    public function delete($id = null)
    {
        if ($this->model->delete($id)) {
            return $this->respondDeleted(['message' => 'Barang sukses dihapus']);
        }
        return $this->fail('Gagal menghapus data');
    }
}
