<?php

namespace app\controllers;

use yii\rest\ActiveController;
use yii\web\Response;

class BookController extends ActiveController
{
    public $modelClass = 'app\models\Book';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['contentNegotiator']['formats']['application/json'] = Response::FORMAT_JSON;
        return $behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index'], $actions['view'], $actions['create'], $actions['update'], $actions['delete']);
        return $actions;
    }

    public function actionList()
    {
        $books = \app\models\Book::find()
            ->joinWith('author')
            ->select('book.*, author.name AS author_name')
            ->asArray()
            ->all();

        return $books;
    }

    public function actionView($id)
    {
        $book = \app\models\Book::find()
            ->joinWith('author')
            ->select('book.*, author.name AS author_name')
            ->where(['book.id' => $id])
            ->asArray()
            ->one();

        if (!$book) {
            throw new \yii\web\NotFoundHttpException('Book not found.');
        }

        return $book;
    }

    public function actionUpdate($id)
    {
        $book = \app\models\Book::findOne($id);

        if (!$book) {
            throw new \yii\web\NotFoundHttpException('Book not found.');
        }

        $book->load(\Yii::$app->getRequest()->getBodyParams(), '');
        if ($book->save()) {
            return ['success' => true];
        } else {
            return ['success' => false, 'errors' => $book->errors];
        }
    }

    public function actionDelete($id)
    {
        $book = \app\models\Book::findOne($id);

        if (!$book) {
            throw new \yii\web\NotFoundHttpException('Book not found.');
        }

        if ($book->delete()) {
            return ['success' => true];
        } else {
            return ['success' => false, 'errors' => 'Failed to delete the book.'];
        }
    }
}
