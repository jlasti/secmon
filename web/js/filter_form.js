var newFilterTemplate = $('.rule').eq(0).clone();

function OnFilterTypeChanged(element) {
	var select = $(element);
	var index = select.attr('data-rule-type');
	var newType = select.val();
	var rule = $('#rules').find('*[data-rule=\''+ index +'\']');
	if (rule.length > 0) {
		rule.find('*[data-type]').each(function(i, e){
			var root = $(e);
			var type = root.attr('data-type');

			// show/hide root element
			if (type == newType) {
				root.removeClass('hide');
			}
			else {
				root.addClass('hide');
			}

			// enable/disable and update selects
			root.find('select').each(function(i, e) {
				var sel = $(e);
				if (type == newType){
					sel.removeAttr('disabled');
				}
				else {
					sel.attr('disabled', 'disabled');
				}

				sel.material_select('destroy');
				sel.material_select();
			});
			// enable/disable inputs
			root.find('input').each(function(i, e) {
				var input = $(e);
				if (type == newType) {
					input.removeAttr('disabled');
				}
				else{
					input.attr('disabled', 'disalbed');
				}
			});
		});
	}
	else
		console.log('Failed to find rule element');
}

function removeRule(element) {
	var el = $(element);
	var ruleId = el.attr('data-rule-remove');
	var index = el.attr('data-rule-index');
	var filterId = el.attr('data-filter-id');
	if (ruleId != -1) {
		window.location = 'delete-rule?id='+ filterId +'&ruleId=' + ruleId;
	} else {
		var rule = $('*[data-rule=\''+ index +'\']');
		if (rule.length > 0) {
			rule.remove();
			var index = 0;
			$('#rules').find('.rule').each(function(i, e) {
				changeRuleIndex(e, index++, -1);
			});
		}
		else
			console.log('Failed to found rule ' + index);
	}
}

function changeRuleIndex(ruleElement, newIndex, removeId) {
	var element = $(ruleElement);
	var oldIndex = element.attr('data-rule');
	if (oldIndex != newIndex) {
		element.attr('data-rule', newIndex);
		element.find('input, select').each(function(i, e) {
			var el = $(e);
			el.prop('name', el.prop('name').replace('['+ oldIndex+']', '[' + newIndex + ']'));
			el.prop('id', el.prop('id').replace('-'+ oldIndex +'-', '-' + newIndex + '-'));
		});
		element.find('a[data-rule-remove]').attr('data-rule-remove', removeId).attr('data-rule-index', newIndex);
		element.find('select[data-rule-type]').each(function(i, e) {
			$(e).attr('data-rule-type', newIndex);
		});
	}
}

$(document).ready(function()
{
	$('#new-rule').click(function() {
		var rules = $('.rule');
		var nr = rules.length;
		var newElement = newFilterTemplate.clone();

		changeRuleIndex(newElement, nr, -1);

		newElement.find('input[type=text]').val('');

		$('#rules').append(newElement);

		newElement.find('select[data-rule-type]').each(function(i, e) {
			$(e).on('change', function() { OnFilterTypeChanged(this); });
		});

		newElement.find('select').each(function(i, e) {
			var sel = $(e);
			sel.material_select('destroy');
			sel.material_select();
		});
	});

	$("#rules").find('select[data-rule-type]').each(function(i, e) {
		$(e).on("change", function() { OnFilterTypeChanged(this); });
	});
});