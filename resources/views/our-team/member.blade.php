@extends('layouts.default-component')

@section('page_class')
    team-member our-team-member our-team @parent
@stop

@section('content')
    <div class="container component-box our-team-container">
        <div class="row">
            <div class="col-12">
                <div class="team-member white-content-block decor-3">
                    <div class="inner">
                        <div class="photo tm-{{ $id }}"></div>
                        <div class="description">
                            <div class="inner-text">
                                <h1>Jonathan Barnes</h1>
                                <div class="links">
                                    <a href="#"><i class="fa fa-envelope"></i></a>
                                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                                    <a href="#"><i class="fab fa-twitter"></i></a>
                                </div>
                                <div class="profile">
                                    <ul class="list-unstyled">
                                        <li>
                                            <div class="line">
                                                <label>Occupation:</label>
                                                <div class="value">Сommander</div>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="line">
                                                <label>Experience:</label>
                                                <div class="value">10 Years</div>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="line">
                                                <label>Phone:</label>
                                                <div class="value">0-800-123-1234</div>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="line">
                                                <label>Email:</label>
                                                <div class="value">Jon@gmail.com</div>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="line">
                                                <label>Location:</label>
                                                <div class="value">Madison Avenue, NY</div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="team-member-details white-content-block">
                    <h2>Biography</h2>
                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt.
                        Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non
                        numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem.</p>
                    <h2>Personal Experience</h2>
                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
                        Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt.
                        Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non
                        numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem.</p>
                </div>
            </div>
        </div>
    </div>
@stop
