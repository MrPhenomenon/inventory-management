<?php

namespace app\controllers;

use app\components\AppController;

/**
 * Replaced by ExpenseController. Kept to avoid broken links.
 */
class OfficeController extends AppController
{
    public function actionIndex()
    {
        return $this->redirect(['expense/index'], 301);
    }

    public function actionAddExpense()
    {
        return $this->redirect(['expense/add'], 301);
    }

    public function actionNil()
    {
        return $this->redirect(['expense/index'], 301);
    }
}
