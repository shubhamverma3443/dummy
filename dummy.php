public function actionReadCsvFileUpdated()
    {
        $this->layout = 'demo';
        $i = 0;
        $model2 = new CSVdataRead();
        if ($model2->load(Yii::$app->request->post())) {
            $model2->file = UploadedFile::getInstance($model2, 'file');
            if ($model2->file) {
                $model2->file->saveAs('@csv/CSVfiles/' . $model2->file);
                $path = Yii::getAlias('@csv');
                $model2->file = $path . '/CSVfiles/' . $model2->file;
                $handle = fopen($model2->file, "r");
                while (($fileop = fgetcsv($handle, 100, ";")) !== FALSE) {
                    $data = explode(",", $fileop[0]);
                    if ($data[0] != 'Id') {
                        $res = Yii::$app->db->createCommand("select id from CSVdata where id='$data[0]'")->queryAll();
                        if ($res) {
                            $sql = "UPDATE CSVdata SET username='$data[1]',identifier='$data[2]',fname='$data[3]',lname='$data[4]' WHERE `id`='$data[0]'";
                        } else {
                            $sql = "INSERT INTO CSVdata(id,username,identifier,fname,lname) VALUES ('$data[0]','$data[1]', '$data[2]','$data[3]','$data[4]')";
                        }
                        Yii::$app->db->createCommand($sql)->execute();
                        $i++;
                    }
                }
                $res2 = Yii::$app->db->createCommand("select id from CSVdata")->queryAll();
                $res2 = count($res2);
                if ($res2 > $i) {
                    $j = (int)$res2 - $i;
                    for ($k = 0; $k <= $j; $k++) {
                        $id = $i + 1;
                        Yii::$app->db->createCommand("delete from CSVdata where id='$id' ")->execute();
                        $i++;
                    }
                }
                Yii::$app->getSession()->setFlash('message', 'Success');
                return $this->redirect(['get-user-data']);
            }
        }
        return $this->render('readCSV', ['model' => $model2]);
    }
