<?php

    use lib\SlimFunctions;
    use app\models\core\BaseModel;
    use app\models\core\ValidableInterface;
    use app\models\core\Pagination\Paginable;
    use app\models\core\Form\FormSearchTypeInterface;
    use app\models\core\Seach\SearchQueryBuilder;

/**
 * creates new entity
 */
$app->map('/admin/new/:entity/', function ($entity) use ($app) {

    $request     = $app->request();
    $entity      = Sanitize::string(trim(strtolower($entity)));
    $ent         = ucfirst($entity);

    $frmLstClass = "\\app\\models\\{$ent}\\{$ent}FormType";
    if (class_exists($frmLstClass)) {
        $formList    = new $frmLstClass;

        $item  = BaseModel::factory(ucfirst($entity))->create();
        $errors= array();

        if ($request->isPost()) {
            $item->bind($request->post());
            if ($item instanceof ValidableInterface) {
                $errors = $item->validate();
            }

            if (!count($errors)) {
                $item->save();
                $app->redirect($app->urlFor('admin.list-entity',array('entity'=>$entity)));
            }
        }

        $app->render('backend/entity/edit.html.twig', array(
            'action'    => 'new',
            'form'      => $formList->getForm(),
            'item'      => $item,
            'entity'    => $entity,
            'errors'    => $errors,
        ));
    } else {
        $app->pass();
    }
})->via('GET','POST')->name('admin.new-entity');