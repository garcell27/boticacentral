<?php

namespace app\controllers;

use app\models\Botica;
use app\models\Laboratorio;
use app\models\ProductosSearch;
use app\models\ProductoStock;
use app\models\StockGeneralSearch;
use Yii;
use app\models\Stock;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
/**
 * StockController implements the CRUD actions for Stock model.
 */
class StockController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access'=>[
                'class'=> AccessControl::className(),
                'rules'=>[
                    [
                        'allow'=>true,
                        'roles'=>['@']
                    ]                    
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Stock models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new StockGeneralSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);

    }

    public function actionVerdetalle($id){
        $model=ProductoStock::findOne($id);
        $boticas=Botica::find()->where([])->all();
        return $this->render('verdetalle', [
            'model' => $model,
        ]);
    }

    public function actionReportes(){
        $laboratorios=Laboratorio::find()->innerJoin('productos','productos.idlaboratorio=laboratorio.idlaboratorio')
            ->innerJoin('unidades','productos.idproducto=unidades.idproducto')
            ->innerJoin('stock','stock.idunidad=unidades.idunidad')
            ->groupBy('idlaboratorio')->orderBy('nombre')->all();
        return $this->render('_reportes',['laboratorios'=>$laboratorios]);
    }


    public function actionTimeMovimientos(){
        $data=Yii::$app->request->post();
        $model=ProductoStock::findOne($data['idproducto']);
        $botica=Botica::findOne($data['idbotica']);
        $pagina=$data['pagina'];
        return $this->renderAjax('_timeMovimientos',[
            'dataMovimientos'=>$model->dataDetalleMov($botica->idbotica,$pagina),
            'stockini'=>$data['stockini'],
            'stockmin'=>$data['stockmin']==-1?null:$data['stockmin'],
            'botica'=>$botica,
            'pagina'=>$pagina,
            'model'=>$model
        ]);
    }


    public function actionUpdateMin($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            if($model->minimo==0 ){
                $model->minimo=null;
            }
            if ($model->save()) {
                return $this->redirect(['verdetalle','id'=>$model->unidad->producto->idproducto]);
            }

        } else {
            return $this->renderAjax('_updatemin', [
                'model' => $model,
            ]);
        }
    }

    /*public function actionGenerarPdf($id){
        $mpdf= new \mPDF();
        $ini=($id-1)*50;
        $laboratorios=Laboratorio::find()->innerJoin('productos','productos.idlaboratorio=laboratorio.idlaboratorio')
            ->innerJoin('unidades','productos.idproducto=unidades.idproducto')
            ->innerJoin('stock','stock.idunidad=unidades.idunidad')->offset($ini)
            ->groupBy('idlaboratorio')->orderBy('nombre')->limit(50)->all();
        $mpdf->SetHeader('SISTEMA DE BOTICA CENTRAL - REPORTE STOCK AL '.date('d/m/Y'));
        $mpdf->SetFooter('Pagina {PAGENO}');
        $mpdf->WriteHtml($this->renderAjax('_stockpdf',['laboratorios'=>$laboratorios]));
        $mpdf->Output();
        exit();
        //return $this->render('_stockpdf',['laboratorios'=>$laboratorios]);
    }

    public function actionGenerarExcel($id){
        $excel = new \PHPExcel();
        $ini=($id-1)*50;
        $excel->getProperties()->setCreator(Yii::$app->user->identity->namefull)
            ->setLastModifiedBy(Yii::$app->user->identity->namefull)
            ->setTitle('REPORTE DE STOCK AL '.date('d/m/Y'));
        $laboratorios=Laboratorio::find()->innerJoin('productos','productos.idlaboratorio=laboratorio.idlaboratorio')
            ->innerJoin('unidades','productos.idproducto=unidades.idproducto')
            ->innerJoin('stock','stock.idunidad=unidades.idunidad')->offset($ini)
            ->groupBy('idlaboratorio')->orderBy('nombre')->limit(50)->all();

        $excel->setActiveSheetIndex(0)
            ->setCellValue('A1', 'LABORATORIO')
            ->setCellValue('B1', 'PRODUCTO')
            ->setCellValue('C1', 'ALMACEN')
            ->setCellValue('D1', 'YANET')
            ->setCellValue('E1', 'SALUD')
            ->setCellValue('F1', 'TOTAL ');
        $excel->getActiveSheet()->setTitle('STOCK');
        $col=2 ;
        foreach ($laboratorios as $lab){
            foreach ($lab->productos as $p){
                $infoalmacen=$p->verStock(1);
                $infoyanet=$p->verStock(2);
                $infosalud=$p->verStock(3);
                if($infoalmacen['existe']){
                    $stockalmacen= $infoalmacen['disponible'];
                }else{
                    $stockalmacen= 0;
                }
                if($infoyanet['existe']){
                    $stockyanet = $infoyanet['disponible'];
                }else{
                    $stockyanet = 0;
                }
                if($infosalud['existe']){
                    $stocksalud = $infosalud['disponible'];
                }else{
                    $stocksalud = 0;
                }
                $stocktotal=$stockalmacen+$stockyanet+$stocksalud;
                $excel->setActiveSheetIndex(0)
                    ->setCellValue('A'.$col, $lab->nombre)
                    ->setCellValue('B'.$col, $p->descripcion)
                    ->setCellValue('C'.$col, $stockalmacen)
                    ->setCellValue('D'.$col, $stockyanet)
                    ->setCellValue('E'.$col, $stocksalud)
                    ->setCellValue('F'.$col, $stocktotal);
                $col++;
            }
        }
        $excel->getActiveSheet()->getStyle('A1:F1')->getFont()->setBold(true);
        $excel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $excel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $excel->getActiveSheet()->setAutoFilter($excel->getActiveSheet()->calculateWorksheetDimension());
        $excel->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="stock.xlsx"');
        header('Cache-Control: max-age=0');
        $objWriter = \PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }


    public function actionConsultarMovimiento(){
        $searchproductos=new ProductosSearch();
        //$productos=$searchproductos->getAllCboProductos();
        $datos=$searchproductos->searchUltimosMovSemana(Yii::$app->request->get());
        return $this->render('consultamov',[
            'model'=>$searchproductos,
            'datos'=>$datos
        ]);
    }
*/

    protected function findModel($id)
    {
        if (($model = Stock::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
