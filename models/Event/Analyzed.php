<?php
/**
 * Created by PhpStorm.
 * User: mkovac
 * Date: 9.1.2019
 * Time: 20:55
 */

namespace app\models\Event;
use app\models\EventsAnalyzedNormalizedList;
use app\models\EventsNormalized;
use Yii;
use yii\db\Exception;

/**
 * This is the model class for table "analyzed_events".
 *
 * @property string $id
 * @property string $time
 * @property string $src_ip
 * @property string $dst_ip
 * @property string $country
 * @property string $city
 * @property string $src_city
 * @property double $src_latitude
 * @property double $latitude
 * @property double $src_longitude
 * @property double $longitude
 * @property string $code
 * @property string $src_code
 * @property integer $events_count
 * @property integer $iteration
 * @property integer $events_normalized_id
 * @property boolean $flag
 */

// TODO - urobit do viac vrstiev, hlbok, zoberie src ako dst a dst ako src a spravi zas dopyt (moznost zacyklenia)
class Analyzed extends \yii\db\ActiveRecord
{
    /**
     * @return string
     */
    public static function tableName()
    {
        return 'analyzed_events';
    }

    /**
     * @inheritdoc
     */

    public function rules()
    {
        return [
            [['time'], 'safe'],
            [['events_count', 'iteration', 'events_normalized_id'], 'integer'],
            [['flag'], 'boolean'],
            [['src_latitude', 'latitude', 'src_longitude', 'longitude'], 'double'],
            [['src_ip', 'dst_ip', 'country','city', 'src_city', 'code', 'src_code'], 'string', 'max' => 255],
        ];
    }

    /**
     * @param $params
     */
    public function Analyse($params){

        // get normalized events, both src and dst by IP
        $eventsNormalizedSrc = self::getNormalizedSRC($params[':id']);
        $eventsNormalizedDst = self::getNormalizedDST($params[':id']);

        // group by IPs
        $countsSRC = [];
        $groupedSrc = self::array_group_byCustom($countsSRC, $eventsNormalizedSrc, "src_ip", "dst_ip");

        $countsDST = [];
        $groupedDst = self::array_group_byCustom($countsDST, $eventsNormalizedDst, "dst_ip", "src_ip");

        // save normalized events to analyzed_normalized_events_list and change status of event to analyzed (true)
        $iteration = 0;
        self::saveAnalyzedSRC($groupedSrc,$countsSRC,$params,$iteration);
        self::saveAnalyzedDST($groupedDst,$countsDST,$params,$iteration);

        // save normalized to analyzed_normalized_events_list
        $id = $params[':id'];
        self::saveNormalized($eventsNormalizedSrc,$id,$iteration);
        self::saveNormalized($eventsNormalizedDst,$id,$iteration);
    }

    /**
     * @param $params
     * @return array
     */
    private static function getNormalizedSRC($params){
        try {
            return Yii::$app->db->createCommand(/** @lang text */
                "SELECT * FROM events_normalized n where n.src_ip = (SELECT src_ip FROM events_normalized where id=:id AND src_ip != '')")
                ->bindValue(':id',$params)
                ->queryAll();
        }
        catch(Exception $e) {
            echo 'Message: ' .$e->getMessage();
        }
    }

    /**
     * @param $params
     * @return array
     */
    private static function getNormalizedDST($params){
        try {
            return Yii::$app->db->createCommand(/** @lang text */
                "SELECT * FROM events_normalized n where n.dst_ip = (SELECT src_ip FROM events_normalized where id=:id AND dst_ip != '')")
                ->bindValue(':id', $params)
                ->queryAll();
        } catch (Exception $e) {
            echo 'Message: ' .$e->getMessage();
        }
    }

    /**
     * @param $eventsNormalized
     * @param $id
     * @param $fieldVal2
     */
    private static function saveNormalized($eventsNormalized, $id, $fieldVal2){
        if(is_array($eventsNormalized) && count($eventsNormalized) > 0){
            for ($i = 0; $i< sizeof($eventsNormalized);$i++){
                $eventsAnalyzedNormalizedList = new EventsAnalyzedNormalizedList;
                $eventsAnalyzedNormalizedList->events_analyzed_iteration = $fieldVal2;
                $eventsAnalyzedNormalizedList->events_analyzed_normalized_id = pg_escape_string($eventsNormalized[$i]["id"]);
                $eventsAnalyzedNormalizedList->events_analyzed_normalized_id = $id;
                $eventsAnalyzedNormalizedList->save();
            }
        }

        if ($id) {
            $model = EventsNormalized::findOne($id);
            $model->analyzed = true;
            $model->save();
        }
    }

    /**
     * @param $groupedSrc
     * @param $counts
     * @param $params
     * @param $fieldVal2
     */
    private static function saveAnalyzedSRC($groupedSrc, $counts, $params, &$fieldVal2){
        // need to know all events with the same analyse (iteration flag)
        $model = Analyzed::find()->orderBy('iteration DESC')->limit(1)->one();
        $fieldVal2 = $model->iteration;
        $fieldVal2++;

        date_default_timezone_set('Europe/Bratislava');
        foreach ($groupedSrc as &$value2) {
            $value3 = &$value2;
            foreach ($value3 as &$value) {
                $model = new Analyzed;
                for ($i = 0; $i < sizeof($value); $i++) {
                    $model->time = date("Y-m-d H:i:s");
                    $model->src_ip = $value[$i]["src_ip"];
                    $model->dst_ip = $value[$i]["dst_ip"];
                    $model->code = $value[$i]["dst_code"];
                    $model->country = $value[$i]["dst_country"];
                    $model->city = $value[$i]["dst_city"];
                    $model->latitude = $value[$i]["dst_latitude"] ?? 0;
                    $model->longitude = $value[$i]["dst_longitude"] ?? 0;
                    $model->src_latitude = $value[$i]["src_latitude"] ?? 0;
                    $model->src_longitude = $value[$i]["src_longitude"] ?? 0;
                    $model->src_city = $value[$i]["src_city"];
                    $model->events_count = $counts[$value[$i]["src_ip"]] ?? 0;
                    $model->events_normalized_id = $params[':id'];
                    $model->iteration = $fieldVal2;
                    $model->flag = false;
                    $model->src_code = $value[$i]["src_code"];
                }
                $model->save();
            }
        }
    }

    /**
     * @param $groupedDst
     * @param $counts
     * @param $params
     * @param $fieldVal2
     */
    private static function saveAnalyzedDST($groupedDst, $counts, $params, $fieldVal2){
        date_default_timezone_set('Europe/Bratislava');
        foreach ($groupedDst as &$value2) {
            $value3 = &$value2;
            foreach ($value3 as &$value) {
                $model = new Analyzed;
                for ($i = 0; $i < sizeof($value); $i++) {
                    $model->time = date("Y-m-d H:i:s");
                    $model->src_ip = $value[$i]["dst_ip"];
                    $model->dst_ip = $value[$i]["src_ip"];
                    $model->code = $value[$i]["src_code"];
                    $model->country = $value[$i]["src_country"];
                    $model->city = $value[$i]["src_city"];
                    $model->latitude = $value[$i]["src_latitude"] ?? 0;
                    $model->longitude = $value[$i]["src_longitude"] ?? 0;
                    $model->src_latitude = $value[$i]["dst_latitude"] ?? 0;
                    $model->src_longitude = $value[$i]["dst_longitude"] ?? 0;
                    $model->src_city = $value[$i]["dst_city"];
                    $model->events_count = $counts[$value[$i]["dst_ip"]] ?? 0;
                    $model->events_normalized_id = $params[':id'];
                    $model->iteration = $fieldVal2;
                    $model->flag = true;
                    $model->src_code = $value[$i]["dst_code"];
                }
                $model->save();
            }
        }
    }

    /**
     * @param $counts
     * @param array $array
     * @param $key
     * @return array|null
     */
    private function array_group_byCustom(&$counts, array $array, $key)
    {
        if (!is_string($key) && !is_int($key) && !is_float($key) && !is_callable($key) ) {
            trigger_error('array_group_by(): The key should be a string, an integer, or a callback', E_USER_ERROR);
            return null;
        }
        $func = (!is_string($key) && is_callable($key) ? $key : null);
        $_key = $key;
        // load the new array, splitting by the target key
        $grouped = [];
        foreach ($array as $value) {
            $key = null;
            if (is_callable($func)) {
                $key = call_user_func($func, $value);
            } elseif (is_object($value) && property_exists($value, $_key)) {
                $key = $value->{$_key};
            } elseif (isset($value[$_key])) {
                $key = $value[$_key];
            }
            if ($key === null) {
                continue;
            }
            $grouped[$key][] = $value;
            if (empty($counts[$key])) {
                $counts[$key] = 0;
            }
            // grouped count
            $counts[$key]++;
        }

        // recursion for more grouping params
        if (func_num_args() > 3) {
            $args = func_get_args();
            foreach ($grouped as $key => $value) {
                $params = array_merge([ $value ], array_slice($args, 3, func_num_args()));
                $grouped[$key] = call_user_func_array('array_group_by', $params);
            }
        }

        return $grouped;
    }
}