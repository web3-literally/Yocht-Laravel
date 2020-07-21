@extends('layouts.default-component')

@section('page_class')
    our-team @parent
@stop

@section('content')
    <div class="container component-box our-team-container">
        <div class="row">
            <div class="col-12">
                <div class="team-list">
                    <div class="team-member">
                        <div class="inner">
                            <div class="photo tm-1"></div>
                            <div class="description">
                                <div class="inner-text">
                                    <label class="title"><a href="{{ route('team-member', 1) }}">Jonathan<br>Barnes</a></label>
                                    <p class="text">Lorem ipsum dolor sit amet<br>consectetur adipisicin</p>
                                    <div class="links">
                                        <a href="#"><i class="fa fa-envelope"></i></a>
                                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                                        <a href="#"><i class="fab fa-twitter"></i></a>
                                    </div>
                                </div>
                                <span class="point"></span>
                            </div>
                        </div>
                    </div>
                    <div class="team-member">
                        <div class="inner">
                            <div class="photo tm-2"></div>
                            <div class="description">
                                <div class="inner-text">
                                    <label class="title"><a href="{{ route('team-member', 2) }}">Jonathan<br>Barnes</a></label>
                                    <p class="text">Lorem ipsum dolor sit amet<br>consectetur adipisicin</p>
                                    <div class="links">
                                        <a href="#"><i class="fa fa-envelope"></i></a>
                                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                                        <a href="#"><i class="fab fa-twitter"></i></a>
                                    </div>
                                </div>
                                <span class="point"></span>
                            </div>
                        </div>
                    </div>
                    <div class="team-member">
                        <div class="inner reverse">
                            <div class="description">
                                <div class="inner-text">
                                    <label class="title"><a href="{{ route('team-member', 3) }}">Jonathan<br>Barnes</a></label>
                                    <p class="text">Lorem ipsum dolor sit amet<br>consectetur adipisicin</p>
                                    <div class="links">
                                        <a href="#"><i class="fa fa-envelope"></i></a>
                                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                                        <a href="#"><i class="fab fa-twitter"></i></a>
                                    </div>
                                </div>
                                <span class="point"></span>
                            </div>
                            <div class="photo tm-3"></div>
                        </div>
                    </div>
                    <div class="team-member">
                        <div class="inner reverse">
                            <div class="description">
                                <div class="inner-text">
                                    <label class="title"><a href="{{ route('team-member', 4) }}">Jonathan<br>Barnes</a></label>
                                    <p class="text">Lorem ipsum dolor sit amet<br>consectetur adipisicin</p>
                                    <div class="links">
                                        <a href="#"><i class="fa fa-envelope"></i></a>
                                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                                        <a href="#"><i class="fab fa-twitter"></i></a>
                                    </div>
                                </div>
                                <span class="point"></span>
                            </div>
                            <div class="photo tm-4"></div>
                        </div>
                    </div>
                    <div class="team-member">
                        <div class="inner">
                            <div class="photo tm-5"></div>
                            <div class="description">
                                <div class="inner-text">
                                    <label class="title"><a href="{{ route('team-member', 5) }}">Jonathan<br>Barnes</a></label>
                                    <p class="text">Lorem ipsum dolor sit amet<br>consectetur adipisicin</p>
                                    <div class="links">
                                        <a href="#"><i class="fa fa-envelope"></i></a>
                                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                                        <a href="#"><i class="fab fa-twitter"></i></a>
                                    </div>
                                </div>
                                <span class="point"></span>
                            </div>
                        </div>
                    </div>
                    <div class="team-member">
                        <div class="inner">
                            <div class="photo tm-6"></div>
                            <div class="description">
                                <div class="inner-text">
                                    <label class="title"><a href="{{ route('team-member', 6) }}">Jonathan<br>Barnes</a></label>
                                    <p class="text">Lorem ipsum dolor sit amet<br>consectetur adipisicin</p>
                                    <div class="links">
                                        <a href="#"><i class="fa fa-envelope"></i></a>
                                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                                        <a href="#"><i class="fab fa-twitter"></i></a>
                                    </div>
                                </div>
                                <span class="point"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
