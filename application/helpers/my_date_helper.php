<?php
if (!function_exists('date_to_string')) {
    /**
     * Fecha en español
     *
     * Formatea una fecha MySql Y-m-d a un string fecha en español.
     * 
     * @author      Diego Romero
     */
    function date_to_string($date, $format = 'd/m/a', $hours = FALSE, $origin_format='Y-m-d')
    {
        if($date == '0000-00-00' )
        {
            return $date;
        }
        
        $date = date_create_from_format($origin_format,$date);

        $array_days = array('Monday' => 'Lunes', 'Tuesday' => 'Martes', 'Wednesday' => 'Miercoles', 'Thursday' => 'Jueves', 'Friday' => 'Viernes', 'Saturday' => 'Sabado', 'Sunday' => 'Domingo');
        $array_months = array('01' => 'Enero', '02' => 'Febrero', '03' => 'Marzo', '04' => 'Abril', '05' => 'Mayo', '06' => 'Junio', '07' => 'Julio', '08' => 'Agosto', '09' => 'Septiembre', '10' => 'Octubre', '11' => 'Noviembre', '12' => 'Diciembre');
        $months_style = array('01' => 'Enero', '02' => 'Febrero', '03' => 'Marzo', '04' => 'Abril', '05' => 'Mayo', '06' => 'Junio', '07' => 'Julio', '08' => 'Agosto', '09' => 'Septiembre', '10' => 'Octubre', '11' => 'Noviembre', '12' => 'Diciembre');
        switch ($format) {
            case 'd/m/a':
                $string_date = $date->format('d/m/Y');
                //Resultado: 25/06/2014
                break;
            case 'd-m-a':
            case 'd-m-Y':
                $string_date = $date->format('d-m-Y');
                //Resultado: 25-06-2014
                break;
            case 'Y-m-d':
                $string_date = $date->format('Y-m-d');
                //Resultado: 25-06-2014
                break;
            case 'd.m.a':
                $string_date = $date->format('d.m.Y');
                //Resultado: 25.06.2014
                break;
            case 'd M a':
                $string_date =  $date->format('d') . ' ' . substr($array_months[$date->format('m')], 0, 3) . ' ' . $date->format('Y');
                //Resultado: 25 Jun 2014
                break;
            case 'd F a':
                $string_date =  $date->format('d') . ' ' . $array_months[$date->format('m')] . ' ' . $date->format('Y');
                //Resultado: 25 Junio 2014
                break;
            case 'D d M a':
                $string_date = substr($array_days[$date->format('l')], 0, 3) . ' ' . $date->format('d') . ' ' . substr($array_months[$date->format('m')], 0, 3) . ' ' . $date->format('Y');
                //Resultado: Mar 25 Jun 2014
                break;
            case 'L d F a':
                $string_date = $array_days[$date->format('l')] . ' ' . $date->format('d') . ' ' . $array_months[$date->format('m')] . ' ' . $date->format('Y');
                //Resultado: Martes 25 Junio 2014
                break;
        }

        if ($hours) {
            $string_date .= ' ' . $date->format('H:i:s');
        }
        return $string_date;
    }

}

if (!function_exists('month_style')) {
    /**
     * Estilo del mes en bootstrap
     *
     * Devuelve el estilo del mes para la etiqueta de bootstrap.
     * 
     * @author      Diego Romero
     */
    function month_style($date)
    {
        $months_style = array('01' => 'bg-light-blue', '02' => 'bg-green', '03' => 'bg-teal', '04' => 'bg-olive', '05' => 'bg-fuchsia', '06' => 'bg-purple', '07' => 'bg-red', '08' => 'bg-blue', '09' => 'bg-lime', '10' => 'bg-maroon', '11' => 'bg-orange', '12' => 'bg-aqua');
        
        if($date == '0000-00-00' )
        {
            return 'bg-gray-active';
        }
        $date = date_create_from_format('Y-m-d',$date);
       
        return isset($months_style[$date->format('m')]) ? $months_style[$date->format('m')] : 'bg-gray-active';
       
    }

    function getWorkingDaysColombia($startDate,$endDate){

        
        $holidays=array("2020-01-01","2020-01-06","2020-03-23","2020-04-09","2020-04-10","2020-05-01","2020-05-25","2020-06-15","2020-06-22","2020-06-29","2020-07-20","2020-08-07","2020-08-17","2020-10-12","2020-11-02","2020-11-16","2020-12-08","2020-12-25");
        // getWorkingDays("2008-12-22","2009-01-02",$holidays)
        
    // do strtotime calculations just once
    $endDate = strtotime($endDate);
    $startDate = strtotime($startDate);


    //The total number of days between the two dates. We compute the no. of seconds and divide it to 60*60*24
    //We add one to inlude both dates in the interval.
    $days = ($endDate - $startDate) / 86400 + 1;

    $no_full_weeks = floor($days / 7);
    $no_remaining_days = fmod($days, 7);

    //It will return 1 if it's Monday,.. ,7 for Sunday
    $the_first_day_of_week = date("N", $startDate);
    $the_last_day_of_week = date("N", $endDate);

    //---->The two can be equal in leap years when february has 29 days, the equal sign is added here
    //In the first case the whole interval is within a week, in the second case the interval falls in two weeks.
    if ($the_first_day_of_week <= $the_last_day_of_week) {
        if ($the_first_day_of_week <= 6 && 6 <= $the_last_day_of_week) $no_remaining_days--;
        if ($the_first_day_of_week <= 7 && 7 <= $the_last_day_of_week) $no_remaining_days--;
    }
    else {
        // (edit by Tokes to fix an Edge case where the start day was a Sunday
        // and the end day was NOT a Saturday)

        // the day of the week for start is later than the day of the week for end
        if ($the_first_day_of_week == 7) {
            // if the start date is a Sunday, then we definitely subtract 1 day
            $no_remaining_days--;

            if ($the_last_day_of_week == 6) {
                // if the end date is a Saturday, then we subtract another day
                $no_remaining_days--;
            }
        }
        else {
            // the start date was a Saturday (or earlier), and the end date was (Mon..Fri)
            // so we skip an entire weekend and subtract 2 days
            $no_remaining_days -= 2;
        }
    }

    //The no. of business days is: (number of weeks between the two dates) * (5 working days) + the remainder
//---->february in none leap years gave a remainder of 0 but still calculated weekends between first and last day, this is one way to fix it
   $workingDays = $no_full_weeks * 5;
    if ($no_remaining_days > 0 )
    {
      $workingDays += $no_remaining_days;
    }

    //We subtract the holidays
    foreach($holidays as $holiday){
        $time_stamp=strtotime($holiday);
        //If the holiday doesn't fall in weekend
        if ($startDate <= $time_stamp && $time_stamp <= $endDate && date("N",$time_stamp) != 6 && date("N",$time_stamp) != 7)
            $workingDays--;
    }

    if($workingDays == 1)
    {
        $workingDays = $workingDays - 1;    
    }
    else{
        $workingDays = $workingDays - 2;
    }
    return $workingDays;
}

function saber_dia($fecha) {//A TRAVES DE UNA FECHA DEVUELVE EL DIA DE LA FECHA QUE SE ENVIA
  $fecha = substr($fecha, 0, 10);
  $dia = date('l', strtotime($fecha));
  $dias_ES = array("Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado", "Domingo");
  $dias_EN = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday");
  $nombredia = str_replace($dias_EN, $dias_ES, $dia);
  return $nombredia;
}

function feriados($fecha){
    $holidays=array("2020-01-01","2020-01-06","2020-03-23","2020-04-09","2020-04-10","2020-05-01","2020-05-25","2020-06-15","2020-06-22","2020-06-29","2020-07-20","2020-08-07","2020-08-17","2020-10-12","2020-11-02","2020-11-16","2020-12-08","2020-12-25");
    $valor = array_search($fecha, $holidays, false);
    if(!empty($valor)){
        return true;
    }else{
        return false;
    }
}

function diasEntreFechas($fecha1,$fecha2){ //DIFERENCIA DE DIAS ENTRE UNA DETERMINADA FECHA Y LA FECHA DE HOY
    $fechaIngreso = new DateTime($fecha1); 
    $fechaAhora = new DateTime($fecha2); 
    $intervalo = $fechaIngreso->diff($fechaAhora);
    $dias_pasados = $intervalo->format('%R%a');
    $dias_pasados = substr($dias_pasados, 1);
    return $dias_pasados;
}
}
