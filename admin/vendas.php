<?php include './layout/header.php'; ?>
<?php include './layout/menu.php'; ?>
<?php
$permissoes = retornaControle('produto');
if (empty($permissoes)) {
	header("Location: adminstrativa.php?msg=Acesso negado.");
}

require 'classes/Produto.php';
require 'classes/Venda.php';
require 'classes/ProdutoDAO.php';
include_once('classes/VendaProduto.php');
require 'classes/VendaDAO.php';
require 'classes/VendaProdutoDAO.php';

$vendaDAO = new VendaDAO();
$vendaProdutoDAO = new VendaProdutoDAO();
if (isset($_GET['pesquisa']) && $_GET['pesquisa'] != '') {
	$vendas = $vendaDAO->listar($_GET['pesquisa']);
} else {
	$vendas = $vendaDAO->listar();
}
/*echo '<pre>';
print_r($vendas);*/

?>
<div class="row" style="margin-top:40px">
	<div class="col-6">
		<h2>Gerenciar produtos</h2>
	</div>
	<div class="col-4">
		<form class="form-inline my-2 my-lg-0">
			<input class="form-control mr-sm-2" name="pesquisa" type="search" placeholder="Pesquisar" aria-label="Pesquisar" value="<?= (isset($_GET['pesquisa']) ? $_GET['pesquisa'] : '') ?>">
			<button class="btn btn-outline-success my-2 my-sm-0" type="submit">
				<i class="fas fa-search"></i>
			</button>
			<a href="./vendas.php" class="btn btn-outline-warning my-2 my-sm-0">
				<i class="fas fa-trash-alt"></i>
			</a>
		</form>
	</div>

</div>
<div class="row">
	<table class="table table-hover table-bordered table-striped table-responsive-lg">
		<thead>
			<tr>
				<th>#ID</th>
				<th>Código</th>
				<th>Data</th>
				<th>Forma Pagamento</th>
				<th>Status</th>
				<th>Cliente</th>
				<th>Ações</th>
			</tr>
		</thead>
		<tbody>
			<?php
			$total_geral = 0;
			foreach ($vendas as $venda) {

				$produtos = $vendaProdutoDAO->listaProdutoCliente($venda->getId());
			?>
				<tr <?php if ($venda->getStatus() == 'Pendente') {
						echo 'class="table-danger"';
					} else if ($venda->getStatus() == 'Iniciada') {
						echo 'class="table-warning"';
					}
					?>>
					<td><?= $venda->getId() ?></td>
					<td><?= $venda->getCodigo() ?></td>
					<td><?= $venda->getDataVenda() ?></td>
					<td><?= $venda->getFormaPagamento() ?></td>
					<td><?= $venda->getStatus() ?></td>
					<td>
						<?= $venda->nome ?> <br>
						<small><?= $venda->email ?></small>
					</td>
					<td>
						<?php if ($permissoes['show']) : ?>
							<a class="btn btn-primary" href="#" id="pedidos" data-toggle="modal" data-target="#listaprodutos<?= $venda->getId(); ?>">
								<i class="fas fa-eye"></i>
							</a>
						<?php endif; ?>
						<?php if ($permissoes['update'] || $permissoes['show']) : ?>
							<a href="controle_venda.php?acao=editar&id=<?= $venda->getId() ?>&status=Finalizada" class="btn btn-success" data-toggle="tooltip" title="Finalizar venda" onclick="return confirm('Deseja realmente finalizar?')">
								<i class="fas fa-thumbs-up"></i>
							</a>
							<a href="controle_venda.php?acao=editar&id=<?= $venda->getId() ?>&status=Pendente" class="btn btn-warning" data-toggle="tooltip" title="Venda pendente" onclick="return confirm('Deseja realmente marcar pendência?')">
								<i class="fas fa-thumbs-down"></i>
							</a>
						<?php endif; ?>
						<?php if ($permissoes['delete']) : ?>
							<a href="controle_venda.php?acao=deletar&id=<?= $venda->getId() ?>" onclick="return confirm('Deseja realmente excluir?')" class="btn btn-danger" data-toggle="tooltip" title="Excluir produto">
								<i class="fas fa-trash-alt"></i>
							</a>
						<?php endif; ?>

						<div class="modal fade" id="listaprodutos<?= $venda->getId(); ?>" tabindex="-1" role="dialog" aria-labelledby="labelCompra" aria-hidden="true">
							<div class="modal-dialog modal-lg" role="document">
								<div class="modal-content">
									<div class="modal-header">
										<h5 class="modal-title" id="labelCompra">Lista de produtos</h5>
										<button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
											<span aria-hidden="true">&times;</span>
										</button>
									</div>
									<div class="modal-body" id="tabela_pedidos_modal">
										<table class="table">
											<tr>
												<th>#</th>
												<th>Descrição</th>
												<th>Valor</th>
												<th>Qtd</th>
												<th>Subtotal</th>
											</tr>
											<?php $total = 0;
											$n = 1;

											foreach ($produtos as $key => $prod) : ?>
												<tr>
													<td>#<?= $n; ?></td>
													<td><?= $prod->nome; ?></td>
													<td>R$ <?= $prod->getValor(); ?></td>
													<td><?= $prod->getQtd(); ?></td>
													<td>R$ <?= number_format(($prod->getQtd() * $prod->getValor()), 2, ',', '.'); ?></td>
												</tr>
											<?php
												$n++;
												$total += ($prod->getQtd() * $prod->getValor());
											endforeach;
											?>
											<tr>
												<th class="text-right">Total</th>
												<th colspan="4" class="text-left">R$ <?= number_format($total, 2, ',', '.') ?></th>
											</tr>
										</table>
									</div>
									<div class="modal-footer">
										<button type="button" class="btn btn-danger" data-dismiss="modal">Fechar janela</button>
									</div>
								</div>
							</div>
						</div>


					</td>
				</tr>
			<?php
				$total_geral += $total;
			} ?>
			<tr class="table-success">
				<th colspan="2" class="text-right">Total</th>
				<th colspan="5" class="text-left">R$ <?= number_format($total_geral, 2, ',', '.') ?></th>
			</tr>
		</tbody>
	</table>
</div>

<?php include './layout/footer.php'; ?>