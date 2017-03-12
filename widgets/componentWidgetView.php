  <?php
  use yii\helpers\Json;

  $options =  Json::decode($component->config);
  printf("<div class='grid-item card %s' id='component_%s'>", $options['width'], $component->id);
        ?>
                <!--Main content of component-->
                <div class="card-content">
                    <div class="card-header">
                        <span class="card-title activator grey-text text-darken-4"><span class="nameTitle"><?php  echo $options['name']; ?></span><i class="material-icons right">more_vert</i></span>
                    </div>
                    <div class="card-body">

                        <!--If no filter was added to component-->
                        <?php if ($component->filter_id == null): ?>
                        <div class="section center-align">
                            <a class="waves-effect waves-light btn-large" <?php printf("href='#modal%s'", $component->id) ?>><i class="material-icons right">add_circle_outline</i>Add content</a>

                            <!-- Modal Structure -->
                            <div class="modal" <?php printf("id='modal%s'", $component->id) ?> >
                                <div class="modal-content">
                                    <h4>Content settings</h4>
                                    <div class="row">
                                        <div class="col s12">
                                            <select>
                                                <option value="1">Filter 1</option>
                                            </select>
                                            <label>Filter Select</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <a href="#!" class=" modal-action modal-close waves-effect waves-green btn-flat">Save</a>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
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
                            
                            <div class="input-field col s12 right-align">
                                <button type="button" class="deleteComponentBtn btn waves-effect waves-light red" data-id="<?php  echo $component->id; ?>">
                                    Delete
                                    <i class="material-icons right">delete</i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>