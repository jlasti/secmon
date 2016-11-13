$(document).ready(function()
{
	$('#new-rule').click(function()
	{
		var nr = $('.rule').length;

		var newElement = $('.rule').eq(0).clone();

		newElement.find('input, select').each(function(i, e)
		{
			var e = $(e);

			e.prop('name', e.prop('name').replace('[0]', '[' + nr + ']'));
			e.prop('id', e.prop('id').replace('-0-', '-' + nr + '-'));
		});

		newElement.find('input[type=text]').val('');

		$('#rules').append(newElement);
	});
});