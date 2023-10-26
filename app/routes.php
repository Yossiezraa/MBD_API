<?php

declare(strict_types=1);

use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\ViewUserAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {

    // get
    $app->get('/siswa', function (Request $request, Response $response) {
        $db = $this->get(PDO::class);

        $query = $db->query('CALL selectSiswa()');
        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        $response->getBody()->write(json_encode($results));

        return $response->withHeader("Content-Type", "application/json");
    });

    $app->get('/sekolah', function (Request $request, Response $response) {
        $db = $this->get(PDO::class);

        $query = $db->query('CALL selectSekolah()');
        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        $response->getBody()->write(json_encode($results));

        return $response->withHeader("Content-Type", "application/json");
    });

    $app->get('/kelas', function (Request $request, Response $response) {
        $db = $this->get(PDO::class);

        $query = $db->query('CALL selectKelas()');
        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        $response->getBody()->write(json_encode($results));

        return $response->withHeader("Content-Type", "application/json");
    });

    $app->get('/peminatan', function (Request $request, Response $response) {
        $db = $this->get(PDO::class);

        $query = $db->query('CALL selectPeminatan()');
        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        $response->getBody()->write(json_encode($results));

        return $response->withHeader("Content-Type", "application/json");
    });

    $app->get('/detail_siswa', function (Request $request, Response $response) {
        $db = $this->get(PDO::class);

        $query = $db->query('CALL selectDetailSiswa()');
        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        $response->getBody()->write(json_encode($results));

        return $response->withHeader("Content-Type", "application/json");
    });
    

    // get by id
    $app->get('/siswa/{id}', function (Request $request, Response $response, $args) {
        $db = $this->get(PDO::class);

        $query = $db->prepare('CALL GetSiswaByPeminatan(:p_Id_peminatan)');
        $query->bindParam(':p_Id_peminatan', $args['id'], PDO::PARAM_INT);
        $query->execute();
        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        $response->getBody()->write(json_encode($results[0]));

        return $response->withHeader("Content-Type", "application/json");
    });

    $app->get('/sekolah/{id}', function (Request $request, Response $response, $args) {
        $db = $this->get(PDO::class);

        $query = $db->prepare('CALL GetSiswaBySekolah(:p_Id_asal_sekolah)');
        $query->bindParam(':p_Id_asal_sekolah', $args['id'], PDO::PARAM_INT);
        $query->execute();
        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        $response->getBody()->write(json_encode($results[0]));

        return $response->withHeader("Content-Type", "application/json");
    });

    $app->get('/kelas/{id}', function (Request $request, Response $response, $args) {
        $db = $this->get(PDO::class);

        $query = $db->prepare('CALL GetSiswaByKelas(:p_Id_kelas)');
        $query->bindParam(':p_Id_kelas', $args['id'], PDO::PARAM_INT);
        $query->execute();
        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        $response->getBody()->write(json_encode($results[0]));

        return $response->withHeader("Content-Type", "application/json");
    });

    // post data
    //CALL CreateSiswa
    $app->post('/siswa', function(Request $request, Response $response) {
        try {
            $parseBody = $request->getParsedBody();
            if (
                empty($parseBody['NISN']) ||
                empty($parseBody['Nama']) ||
                empty($parseBody['Tempat_Lahir']) ||
                empty($parseBody['Tanggal_Lahir']) ||
                empty($parseBody['Alamat']) 
            ) {
                throw new Exception("Harap isi semua field.");
            }
    
            $nisn = $parseBody['NISN'];
            $nama = $parseBody['Nama'];
            $tempatLahir = $parseBody['Tempat_Lahir'];
            $tanggalLahir = $parseBody['Tanggal_Lahir'];
            $alamat = $parseBody['Alamat'];
    
            $db = $this->get(PDO::class);
            $query = $db->prepare('CALL CreateSiswa(?, ?, ?, ?)');
    
            $query->execute([$nisn, $nama, $tempatLahir, $tanggalLahir, $alamat]);
    
            $lastId = $nisn;
    
            $response->getBody()->write(json_encode(['message' => 'Data Siswa Tersimpan Dengan ID ' . $lastId]));
    
            return $response->withHeader('Content-Type', 'application/json');
        } catch (Exception $e) {
            $errorResponse = ['error' => $e->getMessage()];
            $response = $response
                ->withStatus(400)
                ->withHeader('Content-Type', 'application/json');
            $response->getBody()->write(json_encode($errorResponse));
            return $response;
        }
    });

    //CALL CreateSekolah
    $app->post('/sekolah', function(Request $request, Response $response) {
        try {
            $parseBody = $request->getParsedBody();
            if (
                empty($parseBody['Id_asal_sekolah']) ||
                empty($parseBody['Asal_sekolah'])
            ) {
                throw new Exception("Harap isi semua field.");
            }
    
            $idasalsekolah = $parseBody['Id_asal_sekolah'];
            $asalsekolah = $parseBody['Asal_sekolah'];
    
            $db = $this->get(PDO::class);
            $query = $db->prepare('CALL CreateSekolah(?, ?, ?, ?)');
    
            $query->execute([$idasalsekolah, $asalsekolah]);
    
            $lastId = $idasalsekolah;
    
            $response->getBody()->write(json_encode(['message' => 'Data Sekolah Tersimpan Dengan ID ' . $lastId]));
    
            return $response->withHeader('Content-Type', 'application/json');
        } catch (Exception $e) {
            $errorResponse = ['error' => $e->getMessage()];
            $response = $response
                ->withStatus(400)
                ->withHeader('Content-Type', 'application/json');
            $response->getBody()->write(json_encode($errorResponse));
            return $response;
        }
    });

    //CALL CreateKelas
    $app->post('/kelas', function(Request $request, Response $response) {
        try {
            $parseBody = $request->getParsedBody();
            if (
                empty($parseBody['Id_kelas']) ||
                empty($parseBody['Nama_kelas']) 
            ) {
                throw new Exception("Harap isi semua field.");
            }
    
            $idkelas = $parseBody['Id_kelas'];
            $namakelas = $parseBody['Nama_kelas'];
    
            $db = $this->get(PDO::class);
            $query = $db->prepare('CALL CreateKelas(?, ?, ?, ?)');
    
            $query->execute([$idkelas, $namakelas]);
    
            $lastId = $idkelas;
    
            $response->getBody()->write(json_encode(['message' => 'Data Kelas Tersimpan Dengan ID ' . $lastId]));
    
            return $response->withHeader('Content-Type', 'application/json');
        } catch (Exception $e) {
            $errorResponse = ['error' => $e->getMessage()];
            $response = $response
                ->withStatus(400)
                ->withHeader('Content-Type', 'application/json');
            $response->getBody()->write(json_encode($errorResponse));
            return $response;
        }
    });

     //CALL CreateKelas
     $app->post('/kelas', function(Request $request, Response $response) {
        try {
            $parseBody = $request->getParsedBody();
            if (
                empty($parseBody['Id_kelas']) ||
                empty($parseBody['Nama_kelas'])
            ) {
                throw new Exception("Harap isi semua field.");
            }
    
            $idkelas = $parseBody['Id_kelas'];
            $namakelas = $parseBody['Nama_kelas'];
    
            $db = $this->get(PDO::class);
            $query = $db->prepare('CALL CreateKelas(?, ?, ?, ?)');
    
            $query->execute([$idkelas, $namakelas]);
    
            $lastId = $idkelas;
    
            $response->getBody()->write(json_encode(['message' => 'Data Kelas Tersimpan Dengan ID ' . $lastId]));
    
            return $response->withHeader('Content-Type', 'application/json');
        } catch (Exception $e) {
            $errorResponse = ['error' => $e->getMessage()];
            $response = $response
                ->withStatus(400)
                ->withHeader('Content-Type', 'application/json');
            $response->getBody()->write(json_encode($errorResponse));
            return $response;
        }
    });

     //CALL CreatePeminatan
     $app->post('/peminatan', function(Request $request, Response $response) {
        try {
            $parseBody = $request->getParsedBody();
            if (
                empty($parseBody['Id_peminatan']) ||
                empty($parseBody['Nama_peminatan'])
            ) {
                throw new Exception("Harap isi semua field.");
            }
    
            $idpeminatan = $parseBody['Id_peminatan'];
            $namapeminatan = $parseBody['Nama_peminatan'];
    
            $db = $this->get(PDO::class);
            $query = $db->prepare('CALL CreatePeminatan(?, ?, ?, ?)');
    
            $query->execute([$idpeminatan, $namapeminatan]);
    
            $lastId = $idpeminatan;
    
            $response->getBody()->write(json_encode(['message' => 'Data Peminatan Tersimpan Dengan ID ' . $lastId]));
    
            return $response->withHeader('Content-Type', 'application/json');
        } catch (Exception $e) {
            $errorResponse = ['error' => $e->getMessage()];
            $response = $response
                ->withStatus(400)
                ->withHeader('Content-Type', 'application/json');
            $response->getBody()->write(json_encode($errorResponse));
            return $response;
        }
    });

    //CALL CreateDetailSiswa
    $app->post('/detail_siswa', function(Request $request, Response $response) {
        try {
            $parseBody = $request->getParsedBody();
            if (
                empty($parseBody['Id_detail_siswa']) ||
                empty($parseBody['NISN']) ||
                empty($parseBody['Alamat_baru'])
            ) {
                throw new Exception("Harap isi semua field.");
            }
    
            $iddetailsiswa = $parseBody['Id_detail_siswa'];
            $nisn = $parseBody['NISN'];
            $alamatbaru = $parseBody['Alamat_baru'];
    
            $db = $this->get(PDO::class);
            $query = $db->prepare('CALL CreateDetailSiswa(?, ?, ?, ?)');
    
            $query->execute([$iddetailsiswa, $nisn, $alamatbaru]);
    
            $lastId = $iddetailsiswa;
    
            $response->getBody()->write(json_encode(['message' => 'Data Detail Siswa Tersimpan Dengan ID ' . $lastId]));
    
            return $response->withHeader('Content-Type', 'application/json');
        } catch (Exception $e) {
            $errorResponse = ['error' => $e->getMessage()];
            $response = $response
                ->withStatus(400)
                ->withHeader('Content-Type', 'application/json');
            $response->getBody()->write(json_encode($errorResponse));
            return $response;
        }
    });


    // put data
    //CALL UpdateSiswa
    $app->put('/siswa/{NISN}', function (Request $request, Response $response, $args) {
        $parsedBody = $request->getParsedBody();
        $currentId = $args['NISN'];
        $nama = $parsedBody["Nama"];
        $jeniskelamin = $parsedBody["Jenis_Kelamin"];
        $tempatLahir = $parsedBody["Tempat_Lahir"];
        $tanggalLahir = $parsedBody["Tanggal_Lahir"];
        $alamat = $parsedBody["Alamat"];
        
        $db = $this->get(PDO::class);
        
        $query = $db->prepare('CALL UpdateSiswa(?, ?, ?, ?)');
        $query->bindParam(1, $currentId, PDO::PARAM_INT);
        $query->bindParam(2, $nama, PDO::PARAM_STR);
        $query->bindParam(3, $jeniskelamin, PDO::PARAM_STR);
        $query->bindParam(4, $tempatLahir, PDO::PARAM_STR);
        $query->bindParam(5, $tanggalLahir, PDO::PARAM_STR);
        $query->bindParam(6, $alamat, PDO::PARAM_STR);
        
        $query->execute();
        
        if ($query) {
            $response->getBody()->write(json_encode(
                [
                    'message' => 'Siswa dengan NISN ' . $currentId . ' telah diupdate'
                ]
            ));
        } else {
            $response->getBody()->write(json_encode(
                [
                    'message' => 'Gagal mengupdate Siswa dengan NISN ' . $currentId
                ]
            ));
        }
        
        return $response->withHeader("Content-Type", "application/json");
    });

    //CALL UpdateSekolah
    $app->put('/sekolah/{Id_asal_sekolah}', function (Request $request, Response $response, $args) {
        $parsedBody = $request->getParsedBody();
        $currentId = $args['Id_asal_sekolah'];
        $asalsekolah = $parsedBody["Asal_sekolah"];
        
        $db = $this->get(PDO::class);
        
        $query = $db->prepare('CALL UpdateSekolah(?, ?, ?, ?)');
        $query->bindParam(1, $currentId, PDO::PARAM_INT);
        $query->bindParam(2, $asalsekolah, PDO::PARAM_STR);
        
        $query->execute();
        
        if ($query) {
            $response->getBody()->write(json_encode(
                [
                    'message' => 'Sekolah dengan id ' . $currentId . ' telah diupdate'
                ]
            ));
        } else {
            $response->getBody()->write(json_encode(
                [
                    'message' => 'Gagal mengupdate Sekolah dengan id ' . $currentId
                ]
            ));
        }
        
        return $response->withHeader("Content-Type", "application/json");
    });


    //CALL UpdateKelas
    $app->put('/kelas/{Id_kelas}', function (Request $request, Response $response, $args) {
        $parsedBody = $request->getParsedBody();
        $currentId = $args['Id_kelas'];
        $namakelas = $parsedBody["Nama_kelas"];
        
        $db = $this->get(PDO::class);
        
        $query = $db->prepare('CALL UpdateKelas(?, ?, ?, ?)');
        $query->bindParam(1, $currentId, PDO::PARAM_INT);
        $query->bindParam(2, $asalsekolah, PDO::PARAM_STR);
        
        $query->execute();
        
        if ($query) {
            $response->getBody()->write(json_encode(
                [
                    'message' => 'Kelas dengan id ' . $currentId . ' telah diupdate'
                ]
            ));
        } else {
            $response->getBody()->write(json_encode(
                [
                    'message' => 'Gagal mengupdate Kelas dengan id ' . $currentId
                ]
            ));
        }
        
        return $response->withHeader("Content-Type", "application/json");
    });

    //CALL UpdatePeminatan
    $app->put('/peminatan/{Id_peminatan}', function (Request $request, Response $response, $args) {
        $parsedBody = $request->getParsedBody();
        $currentId = $args['Id_peminatan'];
        $namapeminatan = $parsedBody["Nama_peminatan"];
        
        $db = $this->get(PDO::class);
        
        $query = $db->prepare('CALL UpdatePeminatan(?, ?, ?, ?)');
        $query->bindParam(1, $currentId, PDO::PARAM_INT);
        $query->bindParam(2, $asalsekolah, PDO::PARAM_STR);
        
        $query->execute();
        
        if ($query) {
            $response->getBody()->write(json_encode(
                [
                    'message' => 'Peminatan dengan id ' . $currentId . ' telah diupdate'
                ]
            ));
        } else {
            $response->getBody()->write(json_encode(
                [
                    'message' => 'Gagal mengupdate Peminatan dengan id ' . $currentId
                ]
            ));
        }
        
        return $response->withHeader("Content-Type", "application/json");
    });

    //CALL UpdateDetailSiswa
    $app->put('/detail_siswa/{Id_detail_siswa}', function (Request $request, Response $response, $args) {
        $parsedBody = $request->getParsedBody();
        $currentId = $args['Id_detail_siswa'];
        $nisn = $parsedBody["NISN"];
        $alamatbaru = $parsedBody["Alamat_baru"];
        
        $db = $this->get(PDO::class);
        
        $query = $db->prepare('CALL UpdateDetailSiswa(?, ?, ?, ?)');
        $query->bindParam(1, $currentId, PDO::PARAM_INT);
        $query->bindParam(2, $nisn, PDO::PARAM_STR);
        $query->bindParam(3, $alamatbaru, PDO::PARAM_STR);
        
        $query->execute();
        
        if ($query) {
            $response->getBody()->write(json_encode(
                [
                    'message' => 'Detail Siswa dengan id ' . $currentId . ' telah diupdate'
                ]
            ));
        } else {
            $response->getBody()->write(json_encode(
                [
                    'message' => 'Gagal mengupdate Detail Siswa dengan id ' . $currentId
                ]
            ));
        }
        
        return $response->withHeader("Content-Type", "application/json");
    });


    //Call DeleteSiswa
    $app->delete('/siswa/{NISN}', function (Request $request, Response $response, $args) {
        $currentId = $args['NISN'];
        $db = $this->get(PDO::class);
        
        try {
            $query = $db->prepare('CALL DeleteSiswa(?)');
            $query->bindParam(1, $currentId, PDO::PARAM_INT);
            $query->execute();
        
            if ($query->rowCount() === 0) {
                $response = $response->withStatus(404);
                $response->getBody()->write(json_encode(
                    [
                        'message' => 'Siswa dengan id ' . $currentId . ' tidak ditemukan'
                    ]
                ));
            } else {
                $response->getBody()->write(json_encode(
                    [
                        'message' => 'Siswa dengan id ' . $currentId . ' telah dihapus dari database'
                    ]
                ));
            }
        } catch (PDOException $e) {
            $response = $response->withStatus(500);
            $response->getBody()->write(json_encode(
                [
                    'message' => 'Database error ' . $e->getMessage()
                ]
            ));
        }
        
        return $response->withHeader("Content-Type", "application/json");
    });

    //Call DeleteSekolah
    $app->delete('/sekolah/{Id_asal_sekolah}', function (Request $request, Response $response, $args) {
        $currentId = $args['Id_asal_sekolah'];
        $db = $this->get(PDO::class);
        
        try {
            $query = $db->prepare('CALL DeleteSekolah(?)');
            $query->bindParam(1, $currentId, PDO::PARAM_INT);
            $query->execute();
        
            if ($query->rowCount() === 0) {
                $response = $response->withStatus(404);
                $response->getBody()->write(json_encode(
                    [
                        'message' => 'Sekolah dengan id ' . $currentId . ' tidak ditemukan'
                    ]
                ));
            } else {
                $response->getBody()->write(json_encode(
                    [
                        'message' => 'Sekolah dengan id ' . $currentId . ' telah dihapus dari database'
                    ]
                ));
            }
        } catch (PDOException $e) {
            $response = $response->withStatus(500);
            $response->getBody()->write(json_encode(
                [
                    'message' => 'Database error ' . $e->getMessage()
                ]
            ));
        }
        
        return $response->withHeader("Content-Type", "application/json");
    });


    //Call DeleteKelas
    $app->delete('/kelas/{Id_kelas}', function (Request $request, Response $response, $args) {
        $currentId = $args['Id_kelas'];
        $db = $this->get(PDO::class);
        
        try {
            $query = $db->prepare('CALL DeleteKelas(?)');
            $query->bindParam(1, $currentId, PDO::PARAM_INT);
            $query->execute();
        
            if ($query->rowCount() === 0) {
                $response = $response->withStatus(404);
                $response->getBody()->write(json_encode(
                    [
                        'message' => 'Kelas dengan id ' . $currentId . ' tidak ditemukan'
                    ]
                ));
            } else {
                $response->getBody()->write(json_encode(
                    [
                        'message' => 'Kelas dengan id ' . $currentId . ' telah dihapus dari database'
                    ]
                ));
            }
        } catch (PDOException $e) {
            $response = $response->withStatus(500);
            $response->getBody()->write(json_encode(
                [
                    'message' => 'Database error ' . $e->getMessage()
                ]
            ));
        }
        
        return $response->withHeader("Content-Type", "application/json");
    });


    //Call DeletePeminatan
    $app->delete('/peminatan/{Id_peminatan}', function (Request $request, Response $response, $args) {
        $currentId = $args['Id_peminatan'];
        $db = $this->get(PDO::class);
        
        try {
            $query = $db->prepare('CALL DeletePeminatan(?)');
            $query->bindParam(1, $currentId, PDO::PARAM_INT);
            $query->execute();
        
            if ($query->rowCount() === 0) {
                $response = $response->withStatus(404);
                $response->getBody()->write(json_encode(
                    [
                        'message' => 'Peminatan dengan id ' . $currentId . ' tidak ditemukan'
                    ]
                ));
            } else {
                $response->getBody()->write(json_encode(
                    [
                        'message' => 'Peminatan dengan id ' . $currentId . ' telah dihapus dari database'
                    ]
                ));
            }
        } catch (PDOException $e) {
            $response = $response->withStatus(500);
            $response->getBody()->write(json_encode(
                [
                    'message' => 'Database error ' . $e->getMessage()
                ]
            ));
        }
        
        return $response->withHeader("Content-Type", "application/json");
    });


    //Call DeleteDetailSiswa
    $app->delete('/detail_siswa/{Id_detail_siswa}', function (Request $request, Response $response, $args) {
        $currentId = $args['Id_detail_siswa'];
        $db = $this->get(PDO::class);
        
        try {
            $query = $db->prepare('CALL DeleteDetailSiswa(?)');
            $query->bindParam(1, $currentId, PDO::PARAM_INT);
            $query->execute();
        
            if ($query->rowCount() === 0) {
                $response = $response->withStatus(404);
                $response->getBody()->write(json_encode(
                    [
                        'message' => 'DetailSiswa dengan id ' . $currentId . ' tidak ditemukan'
                    ]
                ));
            } else {
                $response->getBody()->write(json_encode(
                    [
                        'message' => 'DetailSiswa dengan id ' . $currentId . ' telah dihapus dari database'
                    ]
                ));
            }
        } catch (PDOException $e) {
            $response = $response->withStatus(500);
            $response->getBody()->write(json_encode(
                [
                    'message' => 'Database error ' . $e->getMessage()
                ]
            ));
        }
        
        return $response->withHeader("Content-Type", "application/json");
    });
};
