<?php
function packingImgStringToContainer($imgstring)
{
	$imgstring=explode(':',gzuncompress(base64_decode($imgstring)));

	$container=new Container(array_shift($imgstring),array_shift($imgstring),array_shift($imgstring));
	$crate_count=array_shift($imgstring);
	for($i=0;$i<$crate_count;$i++)
	{
		$crate=new Crate(array_shift($imgstring),array_shift($imgstring));
		$crate->setPosition(array_shift($imgstring),0,array_shift($imgstring));
		$crate->setProductID(array_shift($imgstring),array_shift($imgstring));
		$container->crate_list[]=$crate;
	}
	return $container;
}

function packingContainerToImgString($container)
{
	$crate_list=$container->crate_list;
	$box_list=packingCratesTo2DMap($crate_list);

	$sort=array();
	foreach($crate_list as $key=>$crate)
	{
		$sort[$key]=$crate->getY()+$crate->getHeight();
		$crate_list[$key]->orig_key=$key;
	}
	array_multisort($sort,SORT_ASC,$crate_list);

	foreach($crate_list as $key=>$crate)
		if(count($box_list[$crate->orig_key])==0)
			unset($crate_list[$key]);

	$out_numbers=array();
	$out_numbers[]=intval($container->getWidth());
	$out_numbers[]=intval($container->getDepth());
	$out_numbers[]=intval($container->getHeight());
	$out_numbers[]=count($crate_list);
	foreach($crate_list as $crate)
	{
		$out_numbers[]=intval($crate->getWidth());
		$out_numbers[]=intval($crate->getDepth());
		$out_numbers[]=intval($crate->getX());
		$out_numbers[]=intval($crate->getZ());
		$out_numbers[]=intval($crate->getProductID());
		$out_numbers[]=intval($crate->getSiteID());
	}

	return base64_encode(gzcompress(implode(':',$out_numbers)));
}

function packingCratesTo2DMap($crate_list)
{
	$sort=array();
	foreach($crate_list as $key=>$crate)
	{
		$sort[$key]=$crate->getY()+$crate->getHeight();
		$crate_list[$key]->orig_key=$key;
	}
	array_multisort($sort,SORT_ASC,$crate_list);

	$box_list=array();
	while(($crate1=array_shift($crate_list))!==null)
	{
		$areas=array(array($crate1->getX(), $crate1->getZ(), $crate1->getX()+$crate1->getWidth(), $crate1->getZ()+$crate1->getDepth()));
		foreach($crate_list as $crate2)
			foreach($areas as $area_id=>$area)
				if($area[0]<($crate2->getX()+$crate2->getWidth()) && $area[2]>$crate2->getX() && $area[1]<($crate2->getZ()+$crate2->getDepth()) && $area[3]>$crate2->getZ())
				{
					unset($areas[$area_id]);
					if($crate2->getZ()>$area[1])
					{
						$new_area=$area;
						$new_area[3]=$crate2->getZ();
						$areas[]=$new_area;
					}
					if($crate2->getZ()+$crate2->getDepth()<$area[3])
					{
						$new_area=$area;
						$new_area[1]=$crate2->getZ()+$crate2->getDepth();
						$areas[]=$new_area;
					}
					if($crate2->getX()>$area[0])
					{
						$new_area=$area;
						$new_area[1]=max($crate2->getZ(),$new_area[1]);
						$new_area[2]=$crate2->getX();
						$new_area[3]=min($crate2->getZ()+$crate2->getDepth(),$new_area[3]);
						$areas[]=$new_area;
					}
					if($crate2->getX()+$crate2->getWidth()<$area[2])
					{
						$new_area=$area;
						$new_area[0]=$crate2->getX()+$crate2->getWidth();
						$new_area[1]=max($crate2->getZ(),$new_area[1]);
						$new_area[3]=min($crate2->getZ()+$crate2->getDepth(),$new_area[3]);
						$areas[]=$new_area;
					}
				}
		$box_list[$crate1->orig_key]=$areas;
	}
	return $box_list;
}

function packingOptimisePacking($container_list)
{
	if(count($container_list)<2) return $container_list;

	$optimal_list=$container_list;
	$crate_list=array();
	foreach($container_list as $container)
	{
		foreach($container->crate_list as $crate)
			$crate_list[]=$crate;
		$container->crate_list=array();
	}
	$empty_list=$container_list;

	$j=$i=0;
	while($j==0)
	{
		$j=$i;
		$crates_added=0;
		$container_list=$empty_list;

		foreach($crate_list as $crate)
		{
			$count_possibles=0;
			$possibles=array();
			foreach($container_list as $key=>$container)
				$count_possibles+=count($possibles[$key]=$container->findPossible($crate));
			if($count_possibles==0) break;

			$want_pos=$j%$count_possibles;
			$j/=$count_possibles;
			foreach($possibles as $key=>$found_possibles)
			{
				if($want_pos<count($found_possibles))
				{
					$crate->setAngle($found['angle']);
					$container_list[$key]->addCrate($crate,$found['hot_spot']);
					$crates_added++;
					$want_pos=0;
					break;
				}
				else
					$want_pos-=count($found_possibles);
			}
			if($want_pos>0) return $optimal_list;
		}

		if($crates_added==count($crate_list))
		{
			foreach($container_list as $key=>$container)
				if(count($container->crate_list)==0)
					unset($container_list[$key]);
			if(count($container_list)<count($optimal_list))
				$optimal_list=$container_list;
			if($j>0) return $optimal_list;
		}

		$i++;
	}
}

function packingFromPost($posted)
{
	moduleLoad('invoice'); //invoiceProductsFromPost, invoiceFromPost, invoiceFromDatabase
	moduleLoad('product');

	global $error_list;

	if(substr($posted['container_list'],0,11)=='b64_gz_ser:')
		return unserialize(gzuncompress(base64_decode(substr($posted['container_list'],11))));
	if(substr($posted['packing_string'],0,11)=='b64_gz_ser:')
		return unserialize(gzuncompress(base64_decode(substr($posted['packing_string'],11))));

	$total_weight=0.0;
	$crate_list=array();
	$container_list=array();

	if(in_array($posted['packing_action'],array('checkout_nocrating','checkout_crating')))
	{
		foreach($posted['packing_products'] as $product)
		{
			$counter=1;
			$add_qty=$product['product_qty'];
			$product_dims=productDimensionFromDatabase($product['product_id'],$product['sub_id'],true);
			if($posted['packing_action']=='checkout_crating')
				$product_dims=productPackingFromDatabase($product['product_id'],$product['sub_id'],true);
			foreach($product_dims as $dims)
			{
				$row=array($dims['width'],$dims['depth'],$dims['height']);
				sort($row);
				if($row[2]>1201 || $row[1]>238 || $row[0]>235 || $row[0]<=0)
				{
					//Invalid Size
				}
				else
				{
					$crate=new Crate();
					$crate->setProductID($product['product_id'],$product['sub_id']);
					if($dims['carton_units']>0)
					{
						$crate->setVars($dims['carton_width'], $dims['carton_depth'], $dims['carton_height'], $dims['carton_weight'], sprintf('%s (%d units)',$product['product_name'],$dims['carton_units']));
						while($add_qty>=$dims['carton_units'])
						{
							$crate_list[]=$crate;
							$add_qty-=$dims['carton_units'];
						}
					}
					if($add_qty>0)
					{
						$crate->setVars($dims['pack_width'], $dims['pack_depth'], $dims['pack_height'], $dims['weight'], sprintf('%s',$product['product_name']));
						while($add_qty>=1)
						{
							$crate_list[]=$crate;
							$add_qty--;
						}
					}
				}
			}
		}
	}
	elseif(in_array($posted['packing_action'],array('invoice_nocrating','invoice_crating')))
	{
		$invoice=invoiceFromPost($posted);
		if($invoice['site_id']==1) $invoice['site_id']=2;	//Hack for highway entertainment products on highwaygames invoice
		$invoice_products=invoiceProductsFromPost($posted);
		foreach($invoice_products as $product)
		{
			if($product['product_id'] == 'GFR'){
				continue;
			}
			if($product['product_id']>0)
			{
				$counter=1;
				$add_qty=$product['product_qty'];
				$product_dims=productDimensionFromDatabase($product['product_id'],$invoice['site_id'],true);
				if($posted['packing_action']=='invoice_crating')
					$product_dims=productPackingFromDatabase($product['product_id'],$invoice['site_id'],true);

				if(count($product_dims)==0)
				{
					$error_list[]=array(
						4,
						'Size information unavailable for: '.$product['product_qty'].' x '.$product['product_name'].' ('.$product['product_id'].')',
						'Size information unavailable for: '.$product['product_qty'].' x '.htmlspecialchars($product['product_name']).' ('.$product['product_id'].')',
						$product['product_id']
					);
				}
				else
					foreach($product_dims as $dims)
					{
						$row=array($dims['width'],$dims['depth'],$dims['height']);
						sort($row);
						if($row[2]>1201 || $row[1]>238 || $row[0]>235)
							$error_list[]=array(
								4,
								'Object too big to fit in any container: '.$product['product_qty'].' x '.$product['product_name'].' ('.$product['product_id'].')',
								'Object too big to fit in any container: '.$product['product_qty'].' x '.htmlspecialchars($product['product_name']).' ('.$product['product_id'].')',
								$product['product_id']
							);
						elseif($row[0]<=0)
							$error_list[]=array(
								5,
								'Invalid size information for: '.$product['product_qty'].' x '.$product['product_name'].' ('.$product['product_id'].')',
								'Invalid size information for: '.$product['product_qty'].' x '.htmlspecialchars($product['product_name']).' ('.$product['product_id'].')',
								$product['product_id']
							);
						else
						{
							$crate=new Crate();
							$crate->setProductID($product['product_id'],$invoice['site_id']);

							if($dims['carton_units']>0)
							{
								$crate->setVars($dims['carton_width'], $dims['carton_depth'], $dims['carton_height'], $dims['carton_weight'], sprintf('%s (%d units)',$product['product_name'],$dims['carton_units']));
								while($add_qty>=$dims['carton_units'])
								{
									$crate_list[]=$crate;
									$add_qty-=$dims['carton_units'];
								}
							}

							if($add_qty>0)
							{
								$crate->setVars($dims['pack_width'], $dims['pack_depth'], $dims['pack_height'], $dims['weight'], sprintf('%s',$product['product_name']));
								while($add_qty>=1)
								{
									$crate_list[]=$crate;
									$add_qty--;
								}
							}
						}
					}
			}
			else
				$error_list[]=array(
					2,
					'Unknown product in list ('.$product['product_id'].')',
					'Unknown product in list ('.$product['product_id'].')',
					$product['product_id']
				);
		}
	}
	elseif($posted['invoice_id']>0)
	{
		$invoice=invoiceFromDatabase($posted['invoice_id']);
		if(substr($invoice['packing_string'],0,11)=='b64_gz_ser:')
			return unserialize(gzuncompress(base64_decode(substr($invoice['packing_string'],11))));
	}

	if(count($crate_list)>1)
	{
		$order_list=array();
		foreach($crate_list as $crate)
			$order_list[]=$crate->getWidth()*$crate->getDepth()*$crate->getHeight();
		array_multisort($order_list,$crate_list);
		$crate_list=&array_reverse($crate_list);
	}

	//Build an array of standard container sizes
	$standards=array();
//	$standards[]=new Container(25.5,23.5,0.0,1.0,'Mini Satchel');
//	$standards[]=new Container(39.0,28.5,0.0,1.0,'1kg Satchel');
//	$standards[]=new Container(40.5,34.0,0.0,2.0,'2kg Satchel');
//	$standards[]=new Container(45.0,22.0,0.0,3.0,'3kg Satchel');
	$standards[]=new Container(590,235,238,21640,"20' Container");
	$standards[]=new Container(1201,235,238,26500,"40' Container");

	if(in_array($posted['packing_action'],array('invoice_crating','checkout_crating')))
		foreach($standards as $i=>$standard)
		{
			$standards[$i]->max_angle=5;
			$standards[$i]->stackable=true;
		}

	//Find a guestimate solution, by quick packing
	$container_count=0;
	$container_list=array();
	while(count($crate_list))
	{
		$crate=array_shift($crate_list);
		if(count($container_list)>0)
		{
			foreach($container_list as $key=>$container)
			{
				$possibilities=&$container->findPossible($crate,true);
				if(count($possibilities)>0)
				{
					$found=&reset($possibilities);
					$crate->setAngle($found['angle']);
					$container_list[$key]->addCrate($crate,$found['hot_spot']);
					continue(2);
				}
			}
			foreach($container_list as $key=>$container)
				if(($container->standard_id+1)<count($standards))
				{
					//Put this crate back in the queue
					$crate_list[]=$crate;

					//Put this container's crates back in the queue
					foreach($container->crate_list as $crate)
						$crate_list[]=$crate;

					//Resort the queue
					$order_list=array();
					foreach($crate_list as $crate)
						$order_list[]=$crate->getWidth()*$crate->getDepth()*$crate->getHeight();
					array_multisort($order_list,$crate_list);
					$crate_list=&array_reverse($crate_list);

					//Replace the container with a bigger one
					$add_me=$standards[$container->standard_id+1];
					$add_me->standard_id=$container->standard_id+1;
					$container_list[$key]=$add_me;

					continue(2);
				}
		}
		foreach($standards as $key=>$container)
		{
			$possibilities=&$container->findPossible($crate,true);
			if(count($possibilities)>0)
			{
				$found=&reset($possibilities);
				$crate->setAngle($found['angle']);
				$container->addCrate($crate,$found['hot_spot']);
				$container->standard_id=$key;
				$container_list[]=$container;
				continue(2);
			}
		}
	}
	$approximate_containers=count($container_list);

//	return packingOptimisePacking($container_list);
	return $container_list;
}
?>