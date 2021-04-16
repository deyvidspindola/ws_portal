		<style type="text/css">
			.fancytree-container{
				height: auto;
			}
		</style>

		<div id="grupo-conteudo-arvore"  style="overflow-y: auto; overflow-x: auto; width: 100% !important;">
            
            
        <input type="hidden" name="id_meta_selecionada" id="id_meta_selecionada" value="<?php echo $this->param->meta ?>" />
        <input type="hidden" name="id_plano_selecionado" id="id_plano_selecionado" value="<?php echo $this->param->plano ?>" />
        <input type="hidden" name="ano" id="ano" value="<?php echo $this->param->ano ?>" />

		<?php if (count($this->todasArvores) == 0) : ?>

			<div style="margin-top: 222px; text-align: center"><strong>Não há árvore criada.</strong></div>

		<?php else: ?>

		<?php $i = 0; ?>

		<?php $superior = 0; ?>

		<?php foreach ($this->todasArvores AS $i => $arvore) : ?>

			<?php $superior = isset($arvore['arvore']['primeiro']) ? $arvore['arvore']['primeiro']->funcionario_id : null; ?>
			
			<div class="arvore">

				<?php if (count($arvore['arvore']) > 0) : ?>

					<div data-arvoreid= "arvore-<?php echo $i; ?>" class="titulo_arvore"><?php echo trim($arvore['arvore_nome']) ?></div>

					<ul id= "arvore-<?php echo $i; ?>" style="display: none;">

						<li class="folder"> <?php echo $arvore['arvore']['primeiro']->nome ?>

							<?php if (isset($this->metasPlanos[$superior]) || count($arvore['arvore']['segundo'][$superior])>0) : ?>

								<ul>
										<?php if (isset($this->metasPlanos[$superior]['metas']) && count($this->metasPlanos[$superior]['metas']) > 0) : ?>

											<?php foreach($this->metasPlanos[$superior]['metas'] AS $meta) : ?>

												<li  class="folder alvo id-<?php echo $meta['meta_id']; ?>" data-metaid="<?php echo $meta['meta_id']; ?>"><?php echo $meta['meta']; ?>

													<?php if (count($meta['planos']) > 0) : ?>

														<ul>

															<?php foreach ($meta['planos'] AS $plano) :  ?>

																<li class="plano id-<?php echo $plano['plano_id']; ?>" data-planoid="<?php echo $plano['plano_id']; ?>"><?php echo $plano['plano']; ?></li>

															<?php endforeach; ?>

														</ul>

													<?php endif; ?>

												</li>
												
											<?php endforeach; ?>

										<?php endif; ?>
						


										<?php if (count($arvore['arvore']['segundo'][$superior])>0) :  ?>

											<?php foreach($arvore['arvore']['segundo'][$superior] AS $segundo_nivel) : ?>

												<li class="folder subordinado"> <?php echo $segundo_nivel->nome; ?>
													<ul>
														<?php if (isset($this->metasPlanos[$segundo_nivel->funcionario_id]['metas']) && count($this->metasPlanos[$segundo_nivel->funcionario_id]['metas']) > 0) : ?>

															<?php foreach($this->metasPlanos[$segundo_nivel->funcionario_id]['metas'] AS $meta) : ?>

																<li  class="folder alvo id-<?php echo $meta['meta_id']; ?>" data-metaid="<?php echo $meta['meta_id']; ?>"><?php echo $meta['meta']; ?>

																	<?php if (count($meta['planos']) > 0) : ?>

																		<ul>

																			<?php foreach ($meta['planos'] AS $plano) :  ?>

																				<li class="plano id-<?php echo $plano['plano_id']; ?>" data-planoid="<?php echo $plano['plano_id']; ?>"><?php echo $plano['plano']; ?></li>

																			<?php endforeach; ?>

																		</ul>

																	<?php endif; ?>

																</li>
																
															<?php endforeach; ?>

														<?php endif; ?>


														<?php if (isset($arvore['arvore']['terceiro'][$segundo_nivel->funcionario_id])) : ?>

															<?php foreach ($arvore['arvore']['terceiro'][$segundo_nivel->funcionario_id] AS $terceiro_nivel): ?>

																<li data-superior="<?php echo $terceiro_nivel->superior; ?>" data-funcionarioid="<?php echo $terceiro_nivel->funcionario_id; ?>" class="folder subordinado navegacao multiplo"><?php echo $terceiro_nivel->nome; ?></li>

															<?php endforeach; ?>

														<?php endif; ?>

													</ul>
												</li>


											<?php endforeach; ?>

										<?php endif; ?>

								</ul>

							<?php endif; ?>

						</li>

					</ul>
				<?php else: ?>
					<div style="margin-top: 222px; text-align: center"><strong>Não há árvore criada.</strong></div>
				<?php endif; ?>

			</div>
			<?php $i++; ?>
		<?php endforeach; ?>

        <?php endif; ?>
        
        </div>
