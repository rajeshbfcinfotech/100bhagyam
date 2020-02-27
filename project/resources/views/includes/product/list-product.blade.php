                                {{-- If This product belongs to vendor then apply this --}}
                                @if($prod->user_id != 0)

                                {{-- check  If This vendor status is active --}}
                                @if($prod->user->is_vendor == 2)

													<li>
														<div class="single-box">
															<div class="left-area">
																<img src="{{ $prod->photo ? asset('assets/images/thumbnails/'.$prod->thumbnail):asset('assets/images/noimage.png') }}" alt="">
															</div>
															<div class="right-area">
																	<div class="stars">
					                                                  <div class="ratings">
					                                                      <div class="empty-stars"></div>
					                                                      <div class="full-stars" style="width:{{App\Models\Rating::ratings($prod->id)}}%"></div>
					                                                  </div>
																		</div>
																		<h4 class="price">{{ $prod->showPrice() }} <del>{{ $prod->showPreviousPrice() }}</del> </h4>
																		<p class="text"><a href="{{ route('front.product',$prod->slug) }}">{{ strlen($prod->name) > 35 ? substr($prod->name,0,35).'...' : $prod->name }}</a></p>
															</div>
														</div>
													</li>


								@endif

                                {{-- If This product belongs admin and apply this --}}

								@else 

													<li>
														<div class="single-box">
															<div class="left-area">
																<img src="{{ $prod->photo ? asset('assets/images/thumbnails/'.$prod->thumbnail):asset('assets/images/noimage.png') }}" alt="">
															</div>
															<div class="right-area">
																	<div class="stars">
					                                                  <div class="ratings">
					                                                      <div class="empty-stars"></div>
					                                                      <div class="full-stars" style="width:{{App\Models\Rating::ratings($prod->id)}}%"></div>
					                                                  </div>
																		</div>
																		<h4 class="price">{{ $prod->showPrice() }} <del>{{ $prod->showPreviousPrice() }}</del> </h4>
																		<p class="text"><a href="{{ route('front.product',$prod->slug) }}">{{ strlen($prod->name) > 35 ? substr($prod->name,0,35).'...' : $prod->name }}</a></p>
															</div>
														</div>
													</li>
								

								@endif

