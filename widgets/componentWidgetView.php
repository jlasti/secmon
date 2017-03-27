<?php
  use yii\helpers\Json;
  use yii\helpers\Url;

  $loggedUserId = Yii::$app->user->getId();
  $options =  Json::decode($component->config);
  $filters = \app\controllers\FilterController::getFiltersOfUser($loggedUserId);
  $contentTypes = array('table' => 'Table', 'lineChart' => 'Line chart', 'barChart' => 'Bar chart');
?>

<div class='grid-item card <?= $options['width'] ?>' id='component_<?= $component->id ?>'>
    <!--Main content of component-->
    <div class="card-content">
        <div class="card-header">
            <span class="card-title activator grey-text text-darken-4"><span class="nameTitle"><?php  echo $options['name']; ?></span><i class="material-icons right">more_vert</i></span>
            <a href="#modal<?= $component->id ?>" class="btn-floating waves-effect waves-light btn-small blue"
               style="position:absolute; top: 30px; right: 40px; display: <?= $component->filter_id == null ? 'none' : 'block' ?>" id="contentEdit">
                <i class="material-icons">edit</i>
            </a>
        </div>

        <div class="card-body">
            <!--Visible: If no filter was added to component-->
            <div class="section center-align" id="componentContentBodyNew<?= $component->id ?>" style="display: <?= $component->filter_id == null ? 'block' : 'none' ?>">
                <a class="waves-effect waves-light btn-large" <?php printf("href='#modal%s'", $component->id) ?>><i class="material-icons right">add_circle_outline</i>Add content</a>
            </div>

            <!--Visible: If filter was added to component-->
            <div class="section center-align" id="componentContentBody<?= $component->id ?>" style="display: <?= $component->filter_id != null ? 'block' : 'none' ?>">
                <div id="componentLoader" class="preloader-wrapper active" style="display: inline-block">
                    <div class="spinner-layer spinner-blue-only">
                        <div class="circle-clipper left"><div class="circle"></div></div>
                        <div class="gap-patch"><div class="circle"></div></div>
                        <div class="circle-clipper right"><div class="circle"></div></div>
                    </div>
                </div>
                <div id="componentBody" style="display: none">

                </div>
            </div>

            <!-- Modal Structure -->
            <div class="modal" id="modal<?= $component->id ?>">
                <div class="modal-content">
                    <h4>Content settings</h4>

                    <form action="#" id="contentSettingsForm<?= $component->id ?>">
                        <div class="row">
                            <div class=" input-field col s11">
                                <input type="hidden" name="componentId" value="<?= $component->id ?>" />

                                <select id="componentFilterId<?= $component->id ?>" name="filterId">
                                    <?php foreach ($filters as $filter):  ?>
                                        <option value="<?= $filter->id ?>"<?= $component->filter_id == $filter->id ? " selected='selected'" : "" ?>><?= $filter->name; ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="componentFilterId<?= $component->id ?>">Filter Select</label>
                            </div>

                            <div class="input-field col s1">
                                <div class="help-block left-align">
                                    <a href="<?= Url::to(["filter/create"]) ?>" class="btn-floating btn-small waves-effect waves-light red"
                                        title="Create new filter">
                                        <i class="material-icons">add</i>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="input-field col s11">
                                <select id="componenContentTypeId<?= $component->id ?>" name="contentTypeId">
                                <?php foreach ($contentTypes as $key => $type): ?>
                                    <option value="<?= $key ?>"<?= ($options["contentType"] ?? "") == $key ? " selected='selected'" : "" ?>><?= $type ?></option>
                                <?php endforeach; ?>
                                </select>
                                <label for="componenContentTypeId<?php print($component->id); ?>">Content type visualisation</label>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="modal-footer">
                    <div class="left" id="removeComponentContentBtn<?= $component->id ?>" style="display: <?= $component->filter_id == null ? "none" : "block" ?>">
                        <button data-action="removeComponentContent" data-id="<?= $component->id ?>" class="modal-action modal-close waves-effect waves-red btn-flat">Delete content</button>
                    </div>

                    <div class="right">
                        <button data-id="<?= $component->id ?>" data-action="saveComponentContent" class="modal-action modal-close waves-effect waves-green btn-flat">Save</button>

                        <button class=" modal-close waves-effect waves-green btn-flat">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--Component options-->
    <div class="card-reveal">
        <div class="card-header light-blue accent-4">
            <span class="card-title white-text"><span class="nameTitle"><?php  echo $options['name']; ?></span> - options<i class="material-icons right">close</i></span>
        </div>

        <div class="card-body">
            <form class="row componentForm"  data-id="<?php  echo $component->id; ?>">
                <div class="input-field col s12">
                    <label class="active" for="name">Name</label>
                    <input class="nameInput" data-id="component_<?php  echo $component->id; ?>" onfocus="this.select();" onmouseup="return false;" id="name<?php  echo $component->id; ?>" type="text" value="<?php  echo $options['name']; ?>">
                </div>

                <div class="input-field col s12">
                    <label class="active">Select width</label>
                    <select id="width<?php  echo $component->id; ?>" class="widthSelect" data-id="component_<?php  echo $component->id; ?>">
                        <option <?= $options['width'] == '' ? ' selected="selected"' : '' ?> value="">25%</option>
                        <option <?= $options['width'] == 'width2' ? ' selected="selected"' : '' ?> value="width2">50%</option>
                        <option <?= $options['width'] == 'width3' ? ' selected="selected"' : '' ?> value="width3">75%</option>
                        <option <?= $options['width'] == 'width4' ? ' selected="selected"' : '' ?> value="width4">100%</option>
                    </select>
                </div>

                <div class="input-field col s12 center-align">
                    <button type="button" class="deleteComponentBtn btn waves-effect waves-light red" data-id="<?php  echo $component->id; ?>">
                        Delete
                        <i class="material-icons right">delete</i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">

</script>