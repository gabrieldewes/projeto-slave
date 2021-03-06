<script type="text/javascript">
	$(document).ready(function(){ 
		function reloadTableProduct(){
			$.ajax({
				url: "<?= base_url('stock/outputs/');?>",
				type: "POST",
				data: $("table"),
				success: function(data){
					$("#output").html($(data).find("table"));
					console.log($(data).find("table"));
				},
				error: function(){
					console.log(data);
					Materialize.toast('Erro ao recarregar a tabela, atualize a pagina!', 4000);	
				}
			});
		}
		var id_stock;
		$("table").on("click",".delete_stock_btn", function(){
			$('#delete_stock_modal').openModal();	
			id_stock = $(this).attr("id");
		});
		$("#delete_stock").on("click", function(){
			$("#delete_stock").attr("disabled", true);
			$.ajax({
				url: "<?php echo site_url('/StockController/deleteOutputStock'); ?>",
				type: "POST",
				data: {id_stock: id_stock},
				success: function(data){
					$("#delete_stock").attr("disabled", false);
					reloadTableProduct();
					Materialize.toast(data, 3000);
				},
				error: function(data){
					$("#delete_stock").attr("disabled", false);
					console.log(data);
					Materialize.toast('Ação não permitida.', 3000);	
				}
			});
		});
		$("#search_button").click(function(){
			$.ajax({
				url: "<?php echo site_url('/StockController/searchStockOutputByPeople'); ?>",
				type: "POST",
				data: {search_string: $("input[name=search]").val()},
				success: function(data){
					if(data == 'O campo de busca esta vazio'){
						reloadTableProduct();
					}
					var obj = JSON.parse(data);
					if(!obj.length>0){
						Materialize.toast("Nenhuma saida encontrada", 4000);
					}else{
						try{
							$('#output > tbody').html("");
							$("#pagination").html("");
							var items=[]; 	
							$.each(obj, function(i,val){
								explode = val.output_date.split("-");								
								items.push($("<tr><td><a href='<?= base_url('stock/outputs/'); ?>/"+val.id_stock+"'>"+val.name+"</a></td><td>"+explode.reverse().join("/")+"</td><td>R$  "+val.sum_value+"</td><td><a class='delete_stock_btn' id='"+val.id_stock+"' href='#'>Apagar</a></td></tr>"));
							});	
							$('#output > tbody').append.apply($('#output > tbody'), items);
						}catch(e) {		
							alert('Ocorreu algum erro ao carregar as saidas de estoque!');
						}			
					}
				},
				error: function(data){
					console.log(data);
					Materialize.toast("Ocorreu algum erro", 2000);
				}
			});
		});
	});
</script>
<div class="row">
	<h4>Saídas de Estoque</h4>
	<div class="card-panel col s11">
		<div class="input-field col s3">
			<a class="green btn" href="<?=base_url('stock/outputs/create') ?>">Adicionar nova</a>
		</div>
		<div class="input-field col s3">
			<input type="text" name="search" placeholder=" Fornecedor..." required>
		</div>
		<div class="input-field col s1">
			<button href="#" id="search_button" class="btn grey">
				<i class="material-icons">search</i>
			</button>
		</div>
	</div>
</div>

<div class="row">
	<div class="col s11 collection">
		<table id="output" class="bordered highlight">
			<thead>
				<td><strong>Entidade</strong></td>
				<td><strong>Data</strong></td>
				<td><strong>Valor total</strong></td>
				<td><strong>Ações</strong></td>
			</thead>
			<tbody>
				<?php foreach($output_stocks as $row) :?>
					<tr>
						<td><a href="<?= base_url('stock/outputs/'.$row['id_stock']); ?>"><?= $row['name'] ?></a></td>
						<td><?= date('d/m/Y', strtotime($row['output_date'])); ?></td>
						<td><?='R$ ' . number_format($row['sum_value'], 2, ',', '.');?></td>
						<td>
							<a class="delete_stock_btn" id="<?= $row['id_stock']; ?>" href="#">Apagar</a>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
	<div id="delete_stock_modal" class="modal">
		<div class="modal-content">
			<h4>Atenção</h4>
			<div class="">
				<p>O estoque retornará ao estado anterior e isto não poderá ser desfeito.</p>
				<strong><p>Realmente quer apagar esta saída de estoque? </p></strong>
			</div>
		</div>
		<div class="modal-footer">
			<a href="#!" class="modal-action modal-close btn-flat">Cancelar</a>
			<a href="#!" id="delete_stock" class="modal-action modal-close btn red">Apagar</a>
		</div>
	</div>
</div>