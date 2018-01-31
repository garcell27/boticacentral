<?php


namespace app\models;


class TransfExterna extends Transferencia
{
    public function behaviors() { return [];}
    public function rules()
    {
        return [
            [['estado', 'idbotorigen', 'idbotdestino', 'create_by','update_by', 'create_at', 'update_at','origen_conf', 'destino_conf'], 'required'],
            [['create_at','update_at'], 'safe'],
            [['idbotorigen', 'idbotdestino', 'origen_conf', 'destino_conf'], 'integer'],
            [['estado'], 'string', 'max' => 1],
            [['idbotorigen'], 'exist', 'skipOnError' => true, 'targetClass' => Botica::className(), 'targetAttribute' => ['idbotorigen' => 'idbotica']],
            [['idbotdestino'], 'exist', 'skipOnError' => true, 'targetClass' => Botica::className(), 'targetAttribute' => ['idbotdestino' => 'idbotica']],
        ];
    }


    public function genMovSalidas($idconexion){
        $conexion=Conexiones::findOne($idconexion);
        foreach ($this->items as $item){
            $movimiento=MovimientoStock::find()->where([
                'tipo_transaccion'=>'F',
                'idprocedencia'=>$item->iddetalle,
                'idbotica'=>$this->idbotorigen
            ])->one();
            if(!$movimiento){
                $movimiento= new MovimientoGenerado();
                $movimiento->idbotica=$this->idbotorigen;
                $movimiento->create_by=$conexion->create_by;
                $movimiento->update_by=$conexion->create_by;
                $movimiento->fecha=date('Y-m-d H:i:s');
                $movimiento->tipo_transaccion='F';
                $movimiento->idprocedencia=$item->iddetalle;
                $movimiento->idunidad=$item->unidad->producto->undpri->idunidad;
                $movimiento->cantidad=$item->cantidad*$item->unidad->equivalencia;
                $movimiento->save();
            }
            $item->unidad->producto->recalculaStock($this->idbotorigen);
        }
    }
    public function genMovIngresos($idconexion){
        $conexion=Conexiones::findOne($idconexion);
        foreach ($this->items as $item){
            $movimiento=MovimientoStock::find()->where([
                'tipo_transaccion'=>'J',
                'idprocedencia'=>$item->iddetalle,
                'idbotica'=>$this->idbotdestino
            ])->one();
            if(!$movimiento){
                $movimiento= new MovimientoGenerado();
                $movimiento->idbotica=$this->idbotdestino;
                $movimiento->create_by=$conexion->create_by;
                $movimiento->update_by=$conexion->create_by;
                $movimiento->fecha=date('Y-m-d H:i:s');
                $movimiento->tipo_transaccion='J';
                $movimiento->idprocedencia=$item->iddetalle;
                $movimiento->idunidad=$item->unidad->producto->undpri->idunidad;
                $movimiento->cantidad=$item->cantidad*$item->unidad->equivalencia;
                $movimiento->save();
            }
            $item->unidad->producto->recalculaStock($this->idbotdestino);
        }
    }

    public function movSalidas(){
        $movimientos=[];
        foreach ($this->items as $item){
            $mov=MovimientoStock::find()->where([
                'tipo_transaccion'=>'F',
                'idprocedencia'=>$item->iddetalle,
                'idbotica'=>$this->idbotorigen
            ])->one();
            $movimientos[]=$mov->attributes;
        }
        return $movimientos;
    }
    public function movIngresos(){
        $movimientos=[];
        foreach ($this->items as $item){
            $mov=MovimientoStock::find()->where([
                'tipo_transaccion'=>'J',
                'idprocedencia'=>$item->iddetalle,
                'idbotica'=>$this->idbotdestino
            ])->one();
            $movimientos[]=$mov->attributes;
        }
        return $movimientos;
    }

}